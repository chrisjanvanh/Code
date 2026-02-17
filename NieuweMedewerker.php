<?php
require 'backend/config.php';

// Haal alle kolommen op van de tabel Medewerker
$columnsResult = $conn->query("SHOW COLUMNS FROM Medewerker");
$columns = $columnsResult->fetch_all(MYSQLI_ASSOC);

// Kolommen die GEEN checkbox zijn
$exclude = [
    "Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf", "Referentie"
];

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $naam = $_POST['naam'];
    $functie = $_POST['functie'];
    $locatie = $_POST['locatie'];
    $leidinggevende = $_POST['leidinggevende'];
    $bedrijf = $_POST['bedrijf'];
    $referentie = $_POST['referentie'];

    // Dynamisch checkbox‑waarden verzamelen
    $values = [];
    foreach ($columns as $col) {
        $kolom = $col['Field'];
        if (in_array($kolom, $exclude)) continue;

        // Checkbox: aangevinkt = 0, niet aangevinkt = NULL
        $values[$kolom] = isset($_POST[$kolom]) ? 0 : NULL;
    }

    // 1. Check of medewerker al bestaat
    $check = $conn->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
    $check->bind_param("s", $naam);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $melding = "Deze medewerker bestaat al.";
        echo "<script>alert('$melding');</script>";
        $check->close();
    } else {
        $check->close();

        // 2. Dynamische INSERT opbouwen
        $kolomnamen = array_keys($values);
        $kolomnamen_sql = implode(", ", $kolomnamen);
        $placeholders = implode(", ", array_fill(0, count($kolomnamen), "?"));

        $sql = "
            INSERT INTO Medewerker 
            (Naam, Functie, Locatie, Leidinggevende, Bedrijf, Referentie, $kolomnamen_sql)
            VALUES (?, ?, ?, ?, ?, ?, $placeholders)
        ";

        $stmt = $conn->prepare($sql);

        // Typestring opbouwen
        $types = "ssssss" . str_repeat("i", count($values));

        // Parameters samenvoegen
        $params = array_merge(
            [$naam, $functie, $locatie, $leidinggevende, $bedrijf, $referentie],
            array_values($values)
        );

        // Dynamisch binden
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $melding = "Nieuwe medewerker succesvol toegevoegd!";
        } else {
            error_log("Medewerker insert error: " . $stmt->error, 3, __DIR__ . "/error.log");
            $melding = "Er is iets fout gegaan, probeer het later opnieuw.";
            echo "<script>alert('$melding');</script>";
        }

        $stmt->close();
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
    <link rel="stylesheet" href="css/NieuweMedewerker.css">
    <script src="javascript/auth.js"></script>
    <script src="javascript/HuidigeToegang.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
    <script>
        // Laad de opgeslagen naam in het naam veld
        window.addEventListener("load", function() {
            const naamOpgeslagen = localStorage.getItem("naamNieuweMedewerker");
            if (naamOpgeslagen) {
                document.getElementById("naam").value = naamOpgeslagen;
                localStorage.removeItem("naamNieuweMedewerker"); // Wis na gebruik
            }
        });
    </script>
    <header>
        <img src="img/menu.png" alt="Menu button" class="menu-button" set onclick="toggleMenu()">
        <img src="img/logo.svg" alt="VDL Groep Logo">
        <a href="index.php">Homepagina</a>
        <a href="HuidigeToegangen.php" class="current">Medewerkers</a>
        <a href="HardwareToewijzen.php"> Hardware</a>
        <a href="Verantwoordelijke.php">Producten</a>
    </header>

    <div class="content">

    <?php if (!empty($melding)): ?>
        <div class="melding">
            <?= $melding ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h2>Nieuwe medewerker</h2>
        <div id="inputvelden"></div>
        Naam: <br>
        <input type="text" id="naam" name="naam" required><br>
        Functie: <br>
        <input type="text" id="functie" name="functie" required><br>
        Locatie: <br>
        <input type="text" id="locatie" name="locatie" required><br>
        Leidinggevende: <br>
        <input type="text" id="leidinggevende" name="leidinggevende" required><br>
        Bedrijf: <br>
        <input type="text" id="bedrijf" name="bedrijf" required><br><br>
        <h2>De nieuwe medewerker heeft het volgende nodig:</h2>
        <h3>Software & Hardware</h3>

            <?php
            foreach ($columns as $col) {
                $kolom = $col['Field'];

                // Sla velden over die geen checkbox moeten zijn
                if (in_array($kolom, $exclude)) continue;

                // Label netjes maken (PowerBI → PowerBI, Netwerkschijf → Netwerkschijf)
                $label = $kolom;

                echo '
                    <input type="checkbox" id="'.$kolom.'" name="'.$kolom.'">
                    <label for="'.$kolom.'"> '.$label.'</label><br>
                ';
            }
            ?>
<br><br>
        Referentie: <br>
        <input type="text" id="referentie" name="referentie"><br>
        <input type="submit" value="Opslaan">
    </form>
    </div>
</body>
</html>