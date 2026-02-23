<?php
require_once "config.php";

if (!isset($_GET['id'])) {
    die("Geen ID opgegeven.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT BestandNaam, BestandType, BestandData
    FROM MedewerkerBestanden
    WHERE ID = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Geen bestand gevonden.");
}

$stmt->bind_result($naam, $type, $data);
$stmt->fetch();

header("Content-Type: $type");
header("Content-Disposition: attachment; filename=\"$naam\"");
header("Content-Length: " . strlen($data));

echo $data;
exit;
