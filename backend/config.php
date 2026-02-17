<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "217.76.24.141";
$user = "VDL";
$password = "1234";
$database = "VDL";

$conn = new mysqli($host, $user, $password, $database);
?>