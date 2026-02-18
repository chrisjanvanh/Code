<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $serienummer = $_POST['serienummer'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $prijs = $_POST['prijs'];
    $aankoopdatum = $_POST['aankoopdatum'];

    $stmt = $conn->prepare("INSERT INTO Hardware (Serienummer, Merk, Model, Prijs, Aankoopdatum) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $serienummer, $merk, $model, $prijs, $aankoopdatum);

    if ($stmt->execute()) {
        echo "Hardware succesvol toegevoegd";
    } else {
        echo "Fout: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
