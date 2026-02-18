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

$stmt = $conn->prepare("INSERT INTO Product (Product, Contactpersoon, Afdeling) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $product, $contact, $afdeling);
if (!$stmt->execute()) $ok = false;
$stmt->close();

$stmt2 = $conn->prepare("ALTER TABLE Medewerker ADD COLUMN `$product` INT DEFAULT null");
if (!$stmt2->execute()) $ok = false;
$stmt2->close();

if ($ok) {
    echo "OK";
} else {
    echo "FOUT";
}
?>