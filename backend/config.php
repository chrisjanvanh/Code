<?php
$host = "wvanhoutstraat.nl";
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