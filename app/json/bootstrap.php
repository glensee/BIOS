<?php
$errors = require_once '../include/protect_json.php';
require_once '../include/bootstrap.php';

# complete bootstrap
if (!isEmpty($errors)) {
    $result = result([$errors]);
    header('Content-Type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
}
else {
doBootstrap();
}