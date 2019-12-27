<?php
require_once '../include/common.php';

$json = json_decode($_REQUEST['r'], true);
$errors = [require_once '../include/protect_json.php',
           JSONisMissingOrEmpty('userid')];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = result($errors);
} else {
    $id = $json['userid'];
    $studentDAO = new StudentDAO();
    # check if userid is valid
    if ($stu = $studentDAO->retrieve($id)) {
        $ed = (float)$stu->geteDollar();
        $result = [
            'status' => 'success',
            'userid' => $stu->getID(),
            'password' => $stu->getPassword(),
            'name' => $stu->getName(),
            'school' => $stu->getSchool(),
            'edollar' => $ed
        ];
    } else {
        $errors[] = 'invalid userid';
    }
}

if (!isEmpty($errors)) {
    $result = result($errors);
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
?>
