<?php
session_start();

if (!isset($_POST['email'])) {
    http_response_code(400);
    echo "Geen email ontvangen";
    exit;
}

$_SESSION['email'] = $_POST['email'];

echo "OK";
?>