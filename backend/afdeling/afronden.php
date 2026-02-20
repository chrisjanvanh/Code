<?php
require __DIR__ . '/../config.php';

if (!isset($_POST['naam'], $_POST['kolom'], $_POST['waarde'])) {
    echo "FOUT: ontbrekende parameters";
    exit;
}

$naam = $_POST['naam'];
$kolom = $_POST['kolom'];
$waarde = $_POST['waarde'];

// Eerst checken of het NULL moet worden
if ($waarde === "null") {
    $waarde = NULL;
}

// Bind altijd als string zodat NULL niet naar 0 wordt geforceerd
$stmt = $conn->prepare("UPDATE Medewerker SET `$kolom` = ? WHERE Naam = ?");
$stmt->bind_param("ss", $waarde, $naam);

echo $stmt->execute() ? "OK" : "FOUT: " . $stmt->error;
