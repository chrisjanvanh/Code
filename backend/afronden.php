<?php
require 'config.php';

if (!isset($_POST['naam'], $_POST['kolom'], $_POST['waarde'])) {
    echo "FOUT: ontbrekende parameters";
    exit;
}

$naam = $_POST['naam'];
$kolom = $_POST['kolom'];
$waarde = intval($_POST['waarde']);

$sql = "UPDATE Medewerker SET `$kolom` = ? WHERE Naam = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $waarde, $naam);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "FOUT: " . $stmt->error;
}

$stmt->close();
?>