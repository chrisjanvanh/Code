<?php
require_once "config2.php";

if (!isset($_GET['id'])) {
    die("Geen ID opgegeven.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT BestandNaam, BestandType, BestandData
    FROM MedewerkerBestanden
    WHERE ID = ?
");
$stmt->execute([$id]);
$bestand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bestand) {
    die("Geen bestand gevonden.");
}

$naam = $bestand['BestandNaam'];
$type = $bestand['BestandType'];
$data = $bestand['BestandData'];

header("Content-Type: $type");
header("Content-Disposition: attachment; filename=\"$naam\"");
header("Content-Length: " . strlen($data));

echo $data;
exit;
