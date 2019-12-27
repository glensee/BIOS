<?php
require_once 'token.php';
require_once 'common.php';
$errors = isMissingOrEmpty('token');

if (!empty($errors)){
	return $errors;
}
else{
	$token = $_REQUEST['token'];

	# check if token is not valid
	# reply with appropriate JSON error message
	# add your code here
	$valid = verify_token($token);
	if(!$valid){
		return 'invalid token';
	}
	else{
		return '';
	}
}

# this bit below might be useful for protecting the JSON requests and for your project
# it will help to check for more conditions such as

# if the user is not an admin and trying to access admin pages

# if the user is trying to access json services and is not doing it properly

# $pathSegments = explode('/',$_SERVER['PHP_SELF']); # Current url
# $numSegment = count($pathSegments);
# $currentFolder = $pathSegments[$numSegment - 2]; # Current folder
# $page = $pathSegments[$numSegment -1]; # Current page

# you can do things like If ($page == "bootstrap-view.php) {   or
# if ($currentfolder == "json") {

?>