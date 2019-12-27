<?php
require_once '../include/common.php';
require_once '../include/validations.php';

$json = json_decode($_REQUEST['r'], true);
$errors = [JSONisMissingOrEmpty('course'), 
           JSONisMissingOrEmpty('section'),
           require_once '../include/protect_json.php'];
$errors = array_filter($errors);


if (!isEmpty($errors)) {
    $result = result($errors);
}

else {
    $section = $json['section'];
    $course = $json['course'];
    $sectStud = new SectionStudentDAO();
    $courseDAO = new CourseDAO();
    $sectionDAO = new SectionDAO();
    $bidDAO = new BidDAO();
    $studentDAO = new StudentDAO();
    $roundDAO = new RoundDAO();
    $round = $roundDAO->retrieve();

    # Course valid?
    if($courseDAO->retrieve($course)){
    # Section valid?
        if ($sectionDAO->retrieve($course,$section)){
            $bids = $bidDAO->retrieve_section($section,$course);
            # Get the min bid
            $min = min_bid($course,$section);
            # Use clearing price or min bid depending on the round
            $minbid = (!($round[0] == 2 && $round[1] == 'started')) ? (float)$min[2]:(float)$min[1];

            $result = ['status'=>'success',
                       'vacancy'=>$min[0],
                       'min-bid-amount'=> $minbid,
                       'students'=>[]];

            foreach($bids as $bid){

                $id = $bid->getID();
                $amt = $bid->getAmount();
                $balance = $studentDAO->retrieve($id)->geteDollar();

                if ($round[1] == "started" && $round[0] == 1) {
                    $status = 'Pending';
                } else if ($sectStud->retrieve_specific($id, $course, $section) || ($round[0] == 2 && $round[1] != 'Not Started' && $amt >= $min[2])) {
                    $status = "success";
                } else {
                    $status = "fail";
                }
                # Validate if round is 2, inactive and a failed bid
                if (!($round[0] == 2 && $round[1] == 'stopped' && $status == 'fail')){
                    $result['students'][] = ['userid'=>$id,
                    'amount'=>(float)$amt,
                    'balance'=>(float)$balance,
                    'status'=>$status];
                }
            }
        }
        else{
            $errors[] = 'invalid section';
        }
    }
    else{
        $errors[] = 'invalid course';
    }
    if (!isEmpty($errors)) {
        $result = result($errors);
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>