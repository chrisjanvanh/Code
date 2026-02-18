<?php
require 'config.php';

$product = $_POST['product'] ?? '';
$contact = $_POST['contact'] ?? '';
$afdeling = $_POST['afdeling'] ?? '';

if ($product === '') {
    echo "FOUT";
    exit;
}

$ok = true;

// 1. Product toevoegen
$stmt = $conn->prepare("INSERT INTO Product (Product, Contactpersoon, Afdeling) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $product, $contact, $afdeling);
if (!$stmt->execute()) $ok = false;
$stmt->close();

// 2. Kolom toevoegen aan Medewerker
$stmt2 = $conn->prepare("ALTER TABLE `Medewerker` ADD `$product` INT(11) NULL DEFAULT NULL AFTER `Toetsenbord en Muis`");
if (!$stmt2->execute()) $ok = false;
$stmt2->close();

echo $ok ? "OK" : "FOUT";
?>
