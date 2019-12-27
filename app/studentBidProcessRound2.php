<?php

require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/validations.php';

// isMissingOrEmpty(...) is in common.php
$errors = [isMissingOrEmpty('course_code'), isMissingOrEmpty('section'), isMissingOrEmpty('amount')];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: studentBid.php");
    exit();
}

$id = $_SESSION['student']->getID();
$minimum = $_SESSION['minimum'];
$processbid = [$id, $_POST['amount'], $_POST['course_code'], $_POST['section'],$minimum];
$errors = array_merge($errors, bidRound2($processbid, FALSE));
$stuDAO = new StudentDAO();
$stu = $stuDAO->retrieve($id);

if (isEmpty($errors)) {
    $_SESSION['bid_success'] = array($_POST['course_code'], $_POST['section'], $_POST['amount'], $stu->geteDollar());
    $_SESSION['student'] = $stu;
} else {
    $_SESSION['errors'] = $errors;
}

Header("Location: studentBid.php");
exit();
