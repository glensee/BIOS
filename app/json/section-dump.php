<?php
require_once '../include/common.php';

// isMissingOrEmpty(...) is in common.php

$json = json_decode($_REQUEST['r'], true);
$errors = [JSONisMissingOrEmpty('course'),
           JSONisMissingOrEmpty('section'),
           require_once '../include/protect_json.php'];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = result($errors);
} else {
    $section = $json['section'];
    $course = $json['course'];
    $sectStud = new SectionStudentDAO();
    $courseDAO = new CourseDAO();
    $sectionDAO = new SectionDAO();
    $roundDAO = new RoundDAO();
    $round = $roundDAO->retrieve();
    # check if course is within the system's records
    if ($courseDAO->retrieve($course)) {
        # check if section is within the system's records
        if ($sectionDAO->retrieve($course,$section)) {
            $enrollmentSuccess = $sectStud->retrieve_enrollment($course, $section);
            $result = [
                'status' => 'success',
                'students' => []
            ];
            # add each of the students into a dictionary and place them within $result
            foreach ($enrollmentSuccess as $er) {
                $ed = (float)$er->getAmount();
                $userid = $er->getID();
                $amt = $ed;
                $result['students'][] = ['userid' => $userid, 'amount' => $amt];
            }
        } else {
            $errors[] = "invalid section";
        }
    } else {
        $errors[] = "invalid course";
    }
    if (!isEmpty($errors)) {
        $result = result($errors);
    }
}



header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
