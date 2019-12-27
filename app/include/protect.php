<?php
require_once 'common.php';

$pathSegments = explode('/', $_SERVER['PHP_SELF']); # Current url
$numSegment = count($pathSegments);
$currentFolder = $pathSegments[$numSegment - 2]; # Current folder
$page = $pathSegments[$numSegment - 1]; # Current page

$admin_pages = ['adminIndex.php', 'bootstrap.php', 'bootstrapProcess.php','adminBrowseAll.php',
				'adminSearchBid.php','adminSearchSection.php','adminSearchStudent.php','adminNavbar.php'];

if (in_array($page, $admin_pages) && !isset($_SESSION['admin'])){
	$_SESSION['errors'] =  ["Not Admin User"];
	header("Location: login.php");
	exit();
}
elseif (!isset($_SESSION['student']) && !isset($_SESSION['admin'])){
	$_SESSION['errors'] =  ["Please Login"];
	header("Location: login.php");
	exit();
}
?>