<?php
require 'config.php';

$product = $_POST['product'] ?? '';
$contact = $_POST['contact'] ?? '';
$afdeling = $_POST['afdeling'] ?? '';

if ($product === '') {
    echo "FOUT";
    exit;
}

function maakKolomNaam($product) {
    $kolom = strtolower($product);
    $kolom = str_replace(' ', '_', $kolom);
    $kolom = preg_replace('/[^a-z0-9_]/', '', $kolom);
    return $kolom;
}

$kolomnaam = maakKolomNaam($product);

$ok = true;

// 1. Product toevoegen
$stmt = $conn->prepare("INSERT INTO Product (Product, Contactpersoon, Afdeling) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $product, $contact, $afdeling);
if (!$stmt->execute()) $ok = false;
$stmt->close();

// 2. Kolom toevoegen aan Medewerker
$stmt2 = $conn->prepare("ALTER TABLE Medewerker ADD COLUMN `$kolomnaam` INT DEFAULT NULL");
if (!$stmt2->execute()) $ok = false;
$stmt2->close();

echo $ok ? "OK" : "FOUT";
?>
