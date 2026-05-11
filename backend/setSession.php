<?php
require "config2.php";
session_start();

if (!isset($_POST['email'])) {
    http_response_code(400);
    echo "Geen email ontvangen";
    exit;
}

$_SESSION['email'] = $_POST['email'];
$_SESSION['gebruikernaam'] = $_POST['gebruikernaam'];

$email = $_POST['email'] ?? '';
$gebruikernaam = $_POST['gebruikernaam'] ?? '';

$stmt = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
$stmt->execute(["$gebruikernaam is ingelogd met het E-mailadres $email", "Inloggen"]);

echo "OK";
?>