<?php
require 'config.php';

if (!isset($_POST['id'])) {
    http_response_code(400);
    echo "Geen ID ontvangen";
    exit;
}

$id = intval($_POST['id']);

// Haal productnaam op
$stmt = $conn->prepare("SELECT Product FROM Product WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($product);
$stmt->fetch();
$stmt->close();

if (!$product) {
    echo "FOUT";
    exit;
}

$ok = true;

// 1. Product verwijderen uit Product-tabel
$stmt = $conn->prepare("DELETE FROM Product WHERE ID = ?");
$stmt->bind_param("i", $id);
if (!$stmt->execute()) $ok = false;
$stmt->close();

// 2. Kolom verwijderen uit Medewerker
$sql = "ALTER TABLE `Medewerker` DROP COLUMN `$product`";
if (!$conn->query($sql)) {
    $ok = false;
}

echo $ok ? "OK" : "FOUT";
?>
