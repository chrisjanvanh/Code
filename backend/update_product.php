<?php
require 'config.php';

$id = intval($_POST['id']);
$contact = $_POST['contact'] ?? '';
$afdeling = $_POST['afdeling'] ?? '';

$stmt = $conn->prepare("UPDATE Product SET Contactpersoon = ?, Afdeling = ? WHERE ID = ?");
$stmt->bind_param("ssi", $contact, $afdeling, $id);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "FOUT";
}

$stmt->close();
?>