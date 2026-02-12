<?php
$host = "wvanhoutstraat.nl";
$user = "VDL";
$password = "1234";
$database = "VDL";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Verbinding mislukt: " . $conn->connect_error]));
}
else {
    echo "Verbinding succesvol";
}
?>