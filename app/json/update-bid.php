<?php
require_once '../include/common.php';
require_once '../include/validations.php';

// isMissingOrEmpty(...) is in common.php
$json = json_decode($_REQUEST['r'],true);
$errors = [ JSONisMissingOrEmpty ('amount'),
            JSONisMissingOrEmpty ('course'),
            JSONisMissingOrEmpty ('section'),
            require_once '../include/protect_json.php',
            JSONisMissingOrEmpty ('userid')];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = result($errors);
}

else {
    $bid = [$json['userid'],$json['amount'],$json['course'],$json['section']];
    # bid if token is verified, otherwise return error
    $errors = bid($bid,FALSE);
}

$result = (!isEmpty($errors)) ? result($errors) : ["status" => "success"];
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>
