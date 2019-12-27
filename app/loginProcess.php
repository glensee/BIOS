<?php

require_once 'include/common.php';

// isMissingOrEmpty(...) is in common.php
$errors = [ isMissingOrEmpty ('username'), isMissingOrEmpty ('password') ];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: login.php");
    exit();
}

$username = $_POST['username'];
$pwd = $_POST['password'];

if ($username == "admin"){
    $adminDAO = new AdminDAO();
    $admin = $adminDAO->retrieve($username);
    # Check if password matches
    if ($admin->authenticate($pwd)){
        $_SESSION['admin'] = TRUE;
        $redirect = "Location: adminIndex.php";
    }
    else {
        $_SESSION['errors'] = ["invalid password"];
    }
}
else {
    $stuDAO = new StudentDAO();
    $authenticate = $stuDAO->authenticate($username,$pwd);
    # Authenticate if student is in database and password matches
    if ($authenticate == "SUCCESS"){
        $stu = $stuDAO->retrieve($username);
        $redirect = "Location: studentIndex.php";
        $_SESSION['student'] = $stu;
    }
    else {
        $_SESSION['errors'] = [$authenticate];
    }
}
# Redirect them to login.php should there be errors
if (!isEmpty($_SESSION['errors'])){
    header("Location: login.php");
}
# Create token, grab round and redirect to appropriate website
else {
    $roundDAO = new RoundDAO();
    $_SESSION['round'] = $roundDAO->retrieve()[0];
    header($redirect);
}
exit();
?>