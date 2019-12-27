<?php
require_once 'common.php';
$roundDAO = new RoundDAO();
var_dump($_SESSION);
var_dump($roundDAO->retrieveAll());
var_dump($roundDAO->retrieve());

?>