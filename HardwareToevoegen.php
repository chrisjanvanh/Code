<?php
require 'backend/config.php';

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $serienummer = $_POST['serienummer'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $prijs = $_POST['prijs'];
    $aankoopdatum = $_POST['aankoopdatum'];

    // 1. Prijs normaliseren
    $prijs = str_replace(',', '.', $prijs);

    if (!is_numeric($prijs)) {
        $melding = "Prijs is geen geldige waarde.";
        echo "<script>alert('$melding');</script>";
    } else {

        // 2. Check of serienummer al bestaat
        $check = $conn->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
        $check->bind_param("s", $serienummer);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $melding = "Dit serienummer bestaat al. Kies een uniek serienummer.";
            echo "<script>alert('$melding');</script>";
        } else {

            // 3. INSERT uitvoeren
            $stmt = $conn->prepare("INSERT INTO Hardware (Serienummer, Merk, Model, Prijs, Aankoopdatum) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $serienummer, $merk, $model, $prijs, $aankoopdatum);

            if ($stmt->execute()) {
                $melding = "Hardware succesvol toegevoegd!";
            } else {
                // Log technische fout
                error_log("Insert error: " . $stmt->error, 3, __DIR__ . "/error.log");

                // Gebruiksvriendelijke melding
                $melding = "Er is iets fout gegaan bij het opslaan. Probeer het opnieuw.";
                echo "<script>alert('$melding');</script>";
            }

            $stmt->close();
        }

        $check->close();
    }
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
        <a href="index.php" class="logo-link"><img src="img/logo.svg" alt="VDL Groep Logo"></a>
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