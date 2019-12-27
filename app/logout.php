<?php
require_once 'include/common.php';

session_unset();

// redirect
header("Location: login.php");
?>