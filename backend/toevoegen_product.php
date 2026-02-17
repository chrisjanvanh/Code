<?php
require 'config.php';

$product = $_POST['product'] ?? '';
$contact = $_POST['contact'] ?? '';
$afdeling = $_POST['afdeling'] ?? '';

if ($product === '') {
    echo "FOUT";
    exit;
}

$stmt = $conn->prepare("INSERT INTO Product (Product, Contactpersoon, Afdeling) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $product, $contact, $afdeling);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "FOUT";
}

$stmt->close();
?>