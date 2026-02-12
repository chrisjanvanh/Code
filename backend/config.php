<?php
echo "PHP Werkt";

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "vdl.avansacademie.com";
$user = "VDL";
$password = "1234";
$database = "VDL";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo '<script>console.error("DB Error: '.addslashes($conn->connect_error).'");</script>';
} else {
    echo '<script>console.log("DB connected");</script>';
}
?>