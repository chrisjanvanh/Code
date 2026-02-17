<?php
require 'backend/config.php';

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $serienummer = $_POST['serienummer'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $prijs = $_POST['prijs'];
    $aankoopdatum = $_POST['aankoopdatum'];

    $stmt = $conn->prepare("INSERT INTO Hardware (Serienummer, Merk, Model, Prijs, Aankoopdatum) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $serienummer, $merk, $model, $prijs, $aankoopdatum);

    if ($stmt->execute()) {
        $melding = "Hardware succesvol toegevoegd!";
    } else {
        $melding = "Fout: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HardwareToevoegen.css">
    <script src="javascript/auth.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
    <header>
        <img src="img/menu.png" alt="Menu button" class="menu-button" set onclick="toggleMenu()">
        <img src="img/logo.svg" alt="VDL Groep Logo">
        <a href="index.php">Homepagina</a>
        <a href="HuidigeToegangen.php">Medewerkers</a>
        <a href="HardwareToewijzen.php" class="current"> Hardware</a>
        <a href="Verantwoordelijke.php">Producten</a>
    </header>

    <div class="content">

    <?php if (!empty($melding)): ?>
        <div class="melding">
            <?= $melding ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h2>Hardware toevoegen</h2>
        <div id="inputvelden"></div>
        Serienummer: <br>
        <input type="text" id="serienummer" name="serienummer" required><br>
        Merk: <br>
        <input type="text" id="merk" name="merk" required><br>
        Model: <br>
        <input type="text" id="model" name="model" required><br>
        Prijs: <br>
        <input type="text" id="prijs" name="prijs" required><br>
        Aankoopdatum: <br>
        <input type="date" id="aankoopdatum" name="aankoopdatum" required><br><br>
        <input type="submit" value="Opslaan">
    </form>
    </div>
</body>
</html>