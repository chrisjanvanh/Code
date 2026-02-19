<?php
require __DIR__ . '/../config.php';

if (!isset($_POST['naam'], $_POST['kolom'], $_POST['waarde'])) {
    echo "FOUT: ontbrekende parameters";
    exit;
}

$naam = $_POST['naam'];
$kolom = $_POST['kolom'];
$waarde = intval($_POST['waarde']);

$stmt = $conn->prepare("UPDATE Medewerker SET `$kolom` = ? WHERE Naam = ?");
$stmt->bind_param("is", $waarde, $naam);

echo $stmt->execute() ? "OK" : "FOUT: " . $stmt->error;
