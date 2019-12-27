<?php
require_once '../include/common.php';
$errors = require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php
if (!isEmpty($errors)) {
    $result = result([$errors]);
} else {
    $roundDAO = new RoundDAO();
    $round = $roundDAO->retrieve();
    $num = $round[0];
    $status = $round[1];
    # No rounds has started yet
    if ($num == 2 && $status == 'stopped') { // Max rounds is 2 according to documentation
        $result = result(['round 2 ended']);
    } else if ($status == 'Not Started') {
        # start round
        $success = $roundDAO->update($num, 'started');
        # round was not started for whatever reason
        $result = ($success) ? $result = ['status' => 'success', 'round' => (int)$num] : $result = result(['Round did not start']);
        $bidDAO = new BidDAO();
        $minBidDAO = new MinBidDAO();
        $bidDAO->removeAll();
        $minBidDAO->removeAll();
    } else {
        $roundDAO->update($num, 'started');
        $result = ['status' => 'success', 'round' => (int)$roundDAO->retrieve()[0]];
    }
    $_SESSION['round'] = $roundDAO->retrieve()[0];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);