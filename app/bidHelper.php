<?php
require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/validations.php';


$bidDAO = new BidDAO();
$roundDAO = new RoundDAO();
$minBidDAO = new MinBidDAO();
$sectstuDAO = new SectionStudentDAO();

define('studentID', $_SESSION['student']->getID());

function hasUserBidded()
{
    global $bidDAO;
    global $processBidDAO;
    global $roundDAO;
    $round = $roundDAO->retrieve();
    return !empty($bidDAO->retrieve(studentID)) ? TRUE : FALSE;
}

function hasRoundEnded()
{
    global $roundDAO;
    $round = $roundDAO->retrieve();
    return !$round || $round[1] != 'started';
}

function currentBids()
{
    global $bidDAO;
    global $processBidDAO;
    global $roundDAO;
    $round = $roundDAO->retrieve();
    return $bidDAO->retrieve(studentID);
}

function enrollments($id){
    global $sectstuDAO;
    $enrollments = $sectstuDAO->retrieve($id);
}
function biddingResult()
{
    global $bidDAO;
    global $roundDAO;
    global $sectionDAO;
    global $minBidDAO;
    global $sectstuDAO;
    $bids = $bidDAO->retrieve(studentID);
    $result = [];
    $round = $roundDAO->retrieve();
    if ($round[0] == 1 || $round[1] == 'Not Started') {
        foreach ($bids as $bid) {
            $course = $bid->getCode();
            $sect = $bid->getSection();
            $amt = $bid->getAmount();

            if ($round[1] == "started") {
                $status = 'Pending';
            } else if ($sectstuDAO->retrieve_specific(studentID, $course, $sect)) {
                $status = "Success";
            } else {
                $status = "Unsuccessful";
            }
            $result[] = [$course, $sect, $amt, $status];
        }
        return $result;

    }
    else {
        foreach ($bids as $bid) {
            $course = $bid->getCode();
            $sect = $bid->getSection();
            $amt = $bid->getAmount();
            $min = min_bid($course,$sect);
            $clearing = $min[2];
            if ($amt >= $clearing) {
                $status = "Success";
            } else {
                $status = "Unsuccessful";
            }
            $result[] = [$course, $sect, $amt, $status, $min[1]];
        }
        return $result;
    }
}
