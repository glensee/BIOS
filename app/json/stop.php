<?php
require_once '../include/common.php';
require_once '../include/clearing.php';
$errors = require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php
if (!isEmpty($errors)) {
    $result = result([$errors]);
} else {
    $roundDAO = new RoundDAO();
    $round = $roundDAO->retrieve();
    $num = $round[0];
    $status = $round[1];

    # check if there is an ongoing round
    if ($status == 'started') {
        // Add next pending round (note: if-condition because max rounds is 2 according to documentation)
        if ($num < 2) $roundDAO->add($num + 1, 'Not Started');
        // Update status
        $success = $roundDAO->update($num, 'stopped');
        if ($success) {
            $result = [
                'status' => 'success'
            ];
            $n = $num;
            clearing($n);
        }
        # Display error in the event the round did not stop
        else {
            $result = result(['round did not stop']);
        }
    } else {
        $result = result(['round already ended']);
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);