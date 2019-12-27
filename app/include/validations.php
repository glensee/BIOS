<?php

require_once 'common.php';
require_once 'token.php';

function bid_exists($data)
{
    $bidDAO = new BidDAO();
    $bid = $bidDAO->retrieve_userid_code($data[0], $data[2]);
    return $bid;
}

# $bid_section will be an array with userid,course & section

function drop($bid_section,$isBid){
    $courseDAO = new CourseDAO();
    $studentDAO = new StudentDAO();
    $sectionDAO = new SectionDAO();
    $bidDAO = new BidDAO();
    $sectStu = new SectionStudentDAO();
    $roundDAO = new RoundDAO();
    $round = $roundDAO->retrieve();
    $errors = [];
    $id = $bid_section[0];
    $course = $bid_section[1];
    $section = $bid_section[2];

    # valid user?
    if (!$stu = $studentDAO->retrieve($id)){
        $errors[] = 'invalid userid';
    }
    # valid course?
    if (	!$courseDAO->retrieve($course)	){
        $errors[] = "invalid course";
    }
    # valid section?
    else if (	!$sectionDAO->retrieve($course,$section)	){
        $errors[] = "invalid section";
    }
    # round ended?
    if (    $round[1] != 'started'){
        $errors[] = ($isBid) ? "round ended" : "round not active";
    }

    if (    isEmpty($errors)    ){
        $data = ($isBid) ? $bidDAO->retrieve_specific($id,$course,$section) :
                        $sectStu->retrieve_specific($id,$course,$section);
        # Bid/Enrollement exists?
        if ($data){
            $stuAmt = $stu->geteDollar();
            $topUp = $data->getAmount();
            $newAmt = $stuAmt + $topUp;
            $drop = ($isBid) ? $bidDAO->drop($id,$course,$section) :
                            $sectStu->drop($id,$course,$section);

            if ($drop) {
                # top up the student's account
                $studentDAO->update($id,$newAmt);
            }
        }
        elseif ($isBid){
            $errors[] = "no such bid";
        }
    }
    sort($errors);
    return $errors;
}

function min_bid($course,$section){
    $sectionDAO = new SectionDAO();
    $sectionstuDAO = new SectionStudentDAO();
    $minBidDAO = new MinBidDAO();
    $bidDAO = new BidDAO();
    $roundDAO = new RoundDAO();
    $round = $roundDAO->retrieve();
    $section_info = $sectionDAO->retrieve($course, $section);
    // Retrieve specific section
    $sectionSize = $section_info->getSize();
    // Retrieve round 1's enrollment by `course` and `section`

    // Get enrollment size
    $enrollment = $sectionstuDAO->retrieve_enrollment($course, $section);
    $enrollmentSize = count($enrollment);
    // Get vacancy for section
    $vacancy = $sectionSize - $enrollmentSize;

    // Get total bids
    $bids = $bidDAO->retrieve_section($section,$course);
    $totalbids = count($bids);

    // Get minimum bid from minbid table
    $minbid = $minBidDAO->retrieve($course,$section);
    $min = 10.0;

    // First bid?
    $clearingPrice = (!isEmpty($bids)) ? end($bids)->getAmount() : 10.00;
    if ($round[0] == 2 && $round[1] == 'stopped' && $vacancy == 0){
        $clearingPrice = (!isEmpty($bids)) ? $bids[$sectionSize-1]->getAmount() : 10.00;
    }
    // Is the minimum bid of this course and section available?
    if ($minbid){
        // Let min be the last bid + 1 when there are >= bids than vacancy
        if ($totalbids >= $vacancy && $vacancy > 0){
            $i = $vacancy - 1;
            $min = $bids[$i]->getAmount() + 1;
        // Makes the clearing price the lowest successful bid
            while ($totalbids > $vacancy && $bids[$vacancy]->getAmount() == $bids[$i]->getAmount()){
                $i--;
            }
            $clearingPrice = $bids[$i]->getAmount();
        }
        if ($min < $mbid = $minbid->getMinimum()){
            $min = $mbid;
        }
    }
    else {
        $minbid = new MinBid($course,$section,10.00);
        $minBidDAO->add($minbid);
    }

    return [$vacancy,$min,$clearingPrice];
}

# bid will be an array with userid, edollar, course code, section number
function bid($bid,$isBootstrap)
{
    $courseDAO = new CourseDAO();
    $studentDAO = new StudentDAO();
    $sectionDAO = new SectionDAO();
    $bidDAO = new BidDAO();
    $prerequisiteDAO = new PrerequisiteDAO();
    $courseCompletedDAO = new CourseCompletedDAO();
    $roundDAO = new RoundDAO();
    $sectionStudentDAO = new SectionStudentDAO();
    $minBidDAO = new MinBidDAO();
    $round = $roundDAO->retrieve();
    $messages = [];
    $amt = $bid[1];
    # BASIC VALIDATIONS

    # valid userid?
    if (!$stu = $studentDAO->retrieve($bid[0])){
        $messages[] = 'invalid userid';
    }
    # e-dollar is >= 10.00 and < 2 decimal places?
    if ($bid[1] < 10.00 || preg_match('/\.\d{3,}/', $bid[1])) {
        $messages[] = "invalid amount";
    }
    # valid course?
    if (!$course = $courseDAO->retrieve($bid[2])) {
        $messages[] = "invalid course";
    }
    # valid section?
    else if (!$section = $sectionDAO->retrieve($bid[2], $bid[3])) {
        $messages[] = "invalid section";
    }

    if (empty($messages)) {
        # Amt of eDollars stu have
        $student_edollar = $studentDAO->retrieve($bid[0])->geteDollar();
        $update = FALSE;

        # active bidding session?
        if (    $round[1] != "started"    ){
            $messages[] = 'round ended';
        }
        if (empty($messages)){
            # Bid too low?
            $min = min_bid($bid[2],$bid[3]);

            if (    $round[0] == 2 && $amt < $min[1]   ){
                $messages[] = 'bid too low';
            }

            # Enrolled in course?
            if (    $sectionStudentDAO->retrieve_userid_course($bid[0],$bid[2])     ){
                $messages[] = "course enrolled";
            }
            # Vacancy?
            $enrolled = $sectionStudentDAO->retrieve_enrollment($bid[2],$bid[3]);
            $size = $sectionDAO->retrieve($bid[2],$bid[3])->getSize();
            if (    count($enrolled) >= $size   ){
                $messages[] = "no vacancy";
            }
            # Existing bid?
            if (    bid_exists($bid)    ) {
                $prevBid = $bidDAO->retrieve_userid_code($bid[0],$bid[2]);
                # amt of previous bid
                $prevBidAmt = $prevBid->getAmount();
                $update = TRUE;
                # enough e$?
                if (    $student_edollar < ($amt - $prevBidAmt)   ) {
                    $messages[] = ($isBootstrap) ? "not enough e-dollar" : "insufficient e$";
                }
            }
            else {
                # Own school course when round is 1?
                if (    ($round[0] == 1)  && $stu->getSchool() != $course->getSchool()    ){
                    $messages[] = "not own school course";
                }

                # Class timetable clash?
                $start = $section->getStart();
                $end = $section->getEnd();
                $enrolled_courses = $sectionStudentDAO->retrieve($bid[0]);
                $bids = $bidDAO->retrieve($bid[0]);

                foreach ($bids as $b) {
                    $b = $sectionDAO->retrieve($b->getCode(), $b->getSection());
                    $bStart = $b->getStart();
                    $bEnd = $b->getEnd();
                    if (    $b->getDay() == $section->getDay()  ) {
                        if (    (strtotime($start) >= strtotime($bStart) &&
                                strtotime($start) < strtotime($bEnd)) ||
                                (strtotime($end) >= strtotime($bStart) &&
                                strtotime($end) < strtotime($bEnd)) ||
                                (strtotime($start) <= strtotime($bStart) &&
                                strtotime($end) >= strtotime($bEnd))   ) {
                            $messages[] = "class timetable clash";
                            break;
                        }
                    }
                }

                foreach ($enrolled_courses as $e) {
                    $e = $sectionDAO->retrieve($e->getCourse(), $e->getSection());
                    $eStart = $e->getStart();
                    $eEnd = $e->getEnd();
                    if (    $e->getDay() == $section->getDay()  ) {
                        if (    (strtotime($start) >= strtotime($eStart) &&
                                strtotime($start) < strtotime($eEnd)) ||
                                (strtotime($end) >= strtotime($eStart) &&
                                strtotime($end) < strtotime($eEnd)) ||
                                (strtotime($start) <= strtotime($eStart) &&
                                strtotime($end) >= strtotime($eEnd))   ) {
                            $messages[] = "class timetable clash";
                            break;
                        }
                    }
                }

                # Exam timetable clash?
                $start = $course->getExamStart();
                $end = $course->getExamEnd();

                foreach ($bids as $b) {
                    $b = $courseDAO->retrieve($b->getCode());
                    $bStart = $b->getExamStart();
                    $bEnd = $b->getExamEnd();
                    if (    $b->getExamDate() == $course->getExamDate()     ) {
                        if (    (strtotime($start) >= strtotime($bStart) &&
                                strtotime($start) < strtotime($bEnd)) ||
                                (strtotime($end) >= strtotime($bStart) &&
                                strtotime($end) < strtotime($bEnd)) ||
                                (strtotime($start) <= strtotime($bStart) &&
                                strtotime($end) >= strtotime($bEnd))  ) {
                            $messages[] = "exam timetable clash";
                            break;
                        }
                    }
                }

                foreach ($enrolled_courses as $e) {
                    $e = $courseDAO->retrieve($e->getCourse());
                    $eStart = $e->getExamStart();
                    $eEnd = $e->getExamEnd();
                    if (    $e->getExamDate() == $course->getExamDate()  ) {
                        if (    (strtotime($start) >= strtotime($eStart) &&
                                strtotime($start) < strtotime($eEnd)) ||
                                (strtotime($end) >= strtotime($eStart) &&
                                strtotime($end) < strtotime($eEnd)) ||
                                (strtotime($start) <= strtotime($eStart) &&
                                strtotime($end) >= strtotime($eEnd))   ) {
                            $messages[] = "exam timetable clash";
                            break;
                        }
                    }
                }
                # Completed prerequisite?
                $courseCompleted = $courseCompletedDAO->retrieve($bid[0]);
                if (    !isEmpty($prereq = $prerequisiteDAO->retrieve($bid[2]))     ) {
                    foreach ($prereq as $p) {
                        if (    !in_array($p, $courseCompleted)     ) {
                            $messages[] = "incomplete prerequisites";
                            break;
                        }
                    }
                }
                # Completed course?
                if (    in_array($bid[2], $courseCompleted) ) {
                    $messages[] = "course completed";
                }

                # Section limit > 5?
                if (    !isEmpty($bids) && (    count($bids)+count($enrolled_courses)   ) >= 5    ) {
                    $messages[] = "section limit reached";
                }
                # enough e$?
                if (    $student_edollar < $bid[1]  ) {
                    $messages[] = ($isBootstrap) ? "not enough e-dollar" : "insufficient e$";
                }
            }
        }
    }

    if (    isEmpty($messages)     ){
        # Updating bid?
        if ($update) {
            # Deduct or topup?

            if ($amt <= $prevBidAmt) {
                $topUp = $prevBidAmt - $amt;
            }
            else{
                $amt -= $prevBidAmt;
            }

            $bidDAO->update(new Bid($bid[0], $bid[1], $bid[2], $bid[3]));
        }
        # Add bid
        else {
            $bidDAO->add(new Bid($bid[0], $bid[1], $bid[2], $bid[3]));
        }
        # deduct or topup?
        $newAmt = (isset($topUp)) ? $student_edollar + $topUp :
                                    $student_edollar - $amt;
        # Update student amount
        $studentDAO->update($bid[0], $newAmt);
        $minbid = $minBidDAO->retrieve($bid[2], $bid[3]);
        $min = min_bid($bid[2],$bid[3]);
        $min_amt = $min[1];
        if ($minbid->getMinimum() < $min_amt){
            $minbid = new MinBid($bid[2], $bid[3],$min_amt);
            $minBidDAO->update($minbid);
        }
        }
    if (!$isBootstrap){
        sort($messages);
    }
    return $messages;
}
