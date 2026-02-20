<?php
require_once "config.php";

if (!isset($_GET['email'])) {
    die("Geen e-mailadres opgegeven.");
}

$email = $_GET['email'];

// Haal bestand op uit database
$stmt = $conn->prepare("
    SELECT BestandNaam, BestandType, BestandData 
    FROM Medewerker 
    WHERE Email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Geen bestand gevonden voor dit e-mailadres.");
}

$stmt->bind_result($naam, $type, $data);
$stmt->fetch();

// Headers voor download
header("Content-Type: $type");
header("Content-Disposition: attachment; filename=\"$naam\"");
header("Content-Length: " . strlen($data));

// Binary output
echo $data;
exit;
?>
