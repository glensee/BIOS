<?php
require_once '../include/common.php';

// isMissingOrEmpty(...) is in common.php

$json = json_decode($_REQUEST['r'], true);
$errors = [JSONisMissingOrEmpty('course'), 
           JSONisMissingOrEmpty('section'),
           require_once '../include/protect_json.php'];
$errors = array_filter($errors);

$BidDAO = new BidDAO();
$sectStud = new SectionStudentDAO();
$roundDAO = new RoundDAO();
$courseDAO = new CourseDAO();
$round = $roundDAO->retrieve();
$sectionDAO = new SectionDAO();

if (!isEmpty($errors)) {
    $result = result($errors);
} else {
    $course = $json['course'];
    $section = $json['section'];

    # check if course is valid
    if ($courseDAO->retrieve($course)) {
        # check if section is valid
        if ($sectionDAO->retrieve($course,$section)) {
            $bids = $BidDAO->retrieve_section($section, $course);
            $result = [
                'status' => 'success',
                'bids' => []
            ];
            $row = 1;
            foreach ($bids as $bid) {
                $bID = $bid->getID();
                # checks if there is an active round
                if ($round[1] == 'started') {
                    $output = '-';
                } else {
                    # checks if the bid was successful
                    $output = ($enrolled_stud = $sectStud->retrieve_specific($bID, $course, $section)) ? 'in' : 'out';
                }
                # add the bid into $result
                $ed = (float)$bid->getAmount();
                $result['bids'][] = [
                    'row' => $row,
                    'userid' => $bID,
                    'amount' => $ed,
                    'result' => $output
                ];
                $row++;
            }
        } else {
            $errors[] = 'invalid section';
        }
    } else {
        $errors[] = 'invalid course';
    }
    if (!isEmpty($errors)) {
        $result = result($errors);
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
