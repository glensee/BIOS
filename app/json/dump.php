<?php
require_once '../include/common.php';
$errors = require_once '../include/protect_json.php';
// isMissingOrEmpty(...) is in common.php

$sectionStudentDAO = new SectionStudentDAO();
$courseCompletedDAO = new CourseCompletedDAO();
$bidDAO = new BidDAO();
$prerequisiteDAO = new PrerequisiteDAO();
$studentDAO = new StudentDAO();
$sectionDAO = new SectionDAO();
$courseDAO = new CourseDAO();
$roundDAO = new RoundDAO();
$round = $roundDAO->retrieve();

if (!isEmpty($errors)) {
    $result = result([$errors]);
}
else {
    $result = [
        'status' => 'success',
        'course' => [],
        'section' => [],
        'student' => [],
        'prerequisite' => [],
        'bid' => [],
        'completed-course' => [],
        'section-student' => []
    ];
    # Courses list
    $courses = $courseDAO->retrieveAll();
    foreach ($courses as $course) {
        $start = $course->getExamStart();
        $end = $course->getExamEnd();
        $start2 = str_replace(":","",$start);
        $end2 = str_replace(":","",$end);
        $result['course'][] = [
            'course' => $course->getCourse(),
            'school' => $course->getSchool(),
            'title' => $course->getTitle(),
            'description' => $course->getDescription(),
            'exam date' => $course->getExamDate(),
            'exam start' => $start2,
            'exam end' => $end2
        ];
    }
    # Sections list
    $sections = $sectionDAO->retrieveAll();
    $days = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
    foreach ($sections as $section) {
        $start = $section->getStart();
        $end = $section->getEnd();
        $start2 = str_replace(":","",$start);
        $end2 = str_replace(":","",$end);
        $day = $section->getDay();
        $day = $day-1;
        $day = $days[$day];
        $result['section'][] = [
            'course' => $section->getCourse(),
            'section' => $section->getSection(),
            'day' => $day,
            'start' => $start2,
            'end' => $end2,
            'instructor' => $section->getInstructor(),
            'venue' => $section->getVenue(),
            'size' => (int)$section->getSize()
        ];
    }
    # Students list
    $students = $studentDAO->retrieveAll();
    foreach ($students as $student) {
        $ed = (float)$student->geteDollar();
        $result['student'][] = [
            'userid' => $student->getID(),
            'password' => $student->getPassword(),
            'name' => $student->getName(),
            'school' => $student->getSchool(),
            'edollar' => $ed
        ];
    }
    # Prerequisites list
    $prerequisites = $prerequisiteDAO->retrieveAll();
    foreach ($prerequisites as $prerequisite) {
        $result['prerequisite'][] = [
            'course' => $prerequisite->getCourse(),
            'prerequisite' => $prerequisite->getPrerequisite()
        ];
    }
    # Bids list
    $bids = $bidDAO->retrieveAll();
    foreach ($bids as $bid) {
        $ed = (float)$bid->getAmount();
        $result['bid'][] = [
            'userid' => $bid->getID(),
            'amount' => $ed,
            'course' => $bid->getCode(),
            'section' => $bid->getSection()
        ];
    }
    # Courses Completed List
    $coursescompleted = $courseCompletedDAO->retrieveAll();
    foreach ($coursescompleted as $coursecompleted) {
        $result['completed-course'][] = [
            'userid' => $coursecompleted->getID(),
            'course' => $coursecompleted->getCode()
        ];
    }
    # Section-Student List
    $sectionstudents = $sectionStudentDAO->retrieveAll();
    foreach ($sectionstudents as $sectionstudent) {
        $ed = (float)$sectionstudent->getAmount();
        $result['section-student'][] = [
            'userid' => $sectionstudent->getID(),
            'course' => $sectionstudent->getCourse(),
            'section' => $sectionstudent->getSection(),
            'amount' => $ed
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);