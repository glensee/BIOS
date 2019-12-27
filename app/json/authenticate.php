<?php
require_once '../include/common.php';
require_once '../include/token.php';

// isMissingOrEmpty(...) is in common.php

$errors = [ isMissingOrEmpty ('password'),
            isMissingOrEmpty ('username') ];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = result($errors);
}
else {
    $username = $_POST['username'];
    $adminDAO = new AdminDAO();
    $user = $adminDAO->retrieve($username);

    if (    $username == "admin" && $user->authenticate($_POST['password'])     ){
        $token = generate_token($user->getUsername());
        $result = [
            "status" => "success",
            "token" => $token
        ];
    }
    elseif ($username != 'admin'){
        $errors[] = "invalid username";
    }
    else{
        $errors[] = "invalid password";
    }
}

if (!isEmpty($errors)){
    $result = result($errors);
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);

?>