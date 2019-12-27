<?php

require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/validations.php';

// isMissingOrEmpty(...) is in common.php
$errors = [isMissingOrEmpty('course_code'), isMissingOrEmpty('section')];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: studentCourseSearch.php");
    exit();
}

$course = $_POST['course_code'];
$section = $_POST['section'];
$id = $_SESSION['student']->getID();
$sectionDAO = new SectionDAO();
$sectionstuDAO = new SectionStudentDAO();
$minBidDAO = new MinBidDAO();
$bidDAO = new BidDAO();

$section_info = $sectionDAO->retrieve($course, $section);

if ($section_info) {
    $min = min_bid($course,$section);
    $vacancy = $min[0];
    $minimum = $minBidDAO->retrieve($course,$section)->getMinimum();

    if ($minimum < $min[1]){
        $minimum = $min[1];
    }

} else {
    $errors[] = 'Course/Section Not Found';
}

if (isEmpty($errors)) {
    $_SESSION['course'] = [$section_info,$vacancy,$minimum];
} else {
    $_SESSION['errors'] = $errors;
}

Header("Location: studentCourseSearch.php");
exit();
