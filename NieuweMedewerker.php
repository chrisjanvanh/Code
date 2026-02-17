<?php
require 'backend/config.php';

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Tekstvelden
    $naam = $_POST['naam'];
    $functie = $_POST['functie'];
    $locatie = $_POST['locatie'];
    $leidinggevende = $_POST['leidinggevende'];
    $bedrijf = $_POST['bedrijf'];
    $referentie = $_POST['referentie'];

    // Checkboxen → 0 of NULL
    $sap = isset($_POST['sap']) ? 0 : NULL;
    $cip = isset($_POST['cip']) ? 0 : NULL;
    $crm = isset($_POST['crm']) ? 0 : NULL;
    $cpq = isset($_POST['cpq']) ? 0 : NULL;
    $powerbi = isset($_POST['powerbi']) ? 0 : NULL;
    $ad = isset($_POST['ad']) ? 0 : NULL;
    $netwerkschijf = isset($_POST['netwerkschijf']) ? 0 : NULL;
    $myvdl = isset($_POST['myvdl']) ? 0 : NULL;
    $plm = isset($_POST['plm']) ? 0 : NULL;
    $bedrijfsportal = isset($_POST['bedrijfsportal']) ? 0 : NULL;
    $ims = isset($_POST['ims']) ? 0 : NULL;

    $laptop = isset($_POST['laptop']) ? 0 : NULL;
    $telefoon = isset($_POST['telefoon']) ? 0 : NULL;
    $toets = isset($_POST['toets']) ? 0 : NULL;

    // SQL INSERT
    $stmt = $conn->prepare("
        INSERT INTO Medewerker 
        (Naam, Functie, Locatie, Leidinggevende, Bedrijf, Referentie,
         SAP, CIP, CRM, CPQ, PowerBI, VDLAD, Netwerkschijf, MyVDL, PLM, Bedrijfsportal, IMS,
         Laptop, Telefoon, Accessoires)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssiiiiiiiiiiiiii",
        $naam, $functie, $locatie, $leidinggevende, $bedrijf, $referentie,
        $sap, $cip, $crm, $cpq, $powerbi, $ad, $netwerkschijf, $myvdl, $plm, $bedrijfsportal, $ims,
        $laptop, $telefoon, $toets
    );

    if ($stmt->execute()) {
        $melding = "Nieuwe medewerker succesvol toegevoegd!";
    } else {
        error_log("Medewerker insert error: " . $stmt->error, 3, __DIR__ . "/error.log");
        $melding = "Er is iets fout gegaan bij het opslaan.";
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
        <h3>Software</h3>  
        <input type="checkbox" id="sap" name="sap">
        <label for="sap"> SAP</label><br>
        <input type="checkbox" id="cip" name="cip">
        <label for="cip"> CIP</label><br>
        <input type="checkbox" id="crm" name="crm">
        <label for="crm"> CRM</label><br>
        <input type="checkbox" id="cpq" name="cpq">
        <label for="cpq"> CPQ</label><br>
        <input type="checkbox" id="powerbi" name="powerbi">
        <label for="powerbi"> PowerBI</label><br>
        <input type="checkbox" id="ad" name="ad">
        <label for="ad"> VDL AD Acccount</label><br>
        <input type="checkbox" id="netwerkschijf" name="netwerkschijf">
        <label for="netwerkschijf"> Rechten Netwerkschijf</label><br>
        <input type="checkbox" id="myvdl" name="myvdl">
        <label for="myvdl"> MyVDL</label><br>
        <input type="checkbox" id="plm" name="plm">
        <label for="plm"> PLM Windchill</label><br>
        <input type="checkbox" id="bedrijfsportal" name="bedrijfsportal">
        <label for="bedrijfsportal"> Bedrijfsportal Access</label><br>
        <input type="checkbox" id="ims" name="ims">
        <label for="ims"> IMS</label><br>

        <h3>Hardware</h3>
        <input type="checkbox" id="laptop" name="laptop">
        <label for="laptop"> Laptop</label><br>
        <input type="checkbox" id="telefoon" name="telefoon">
        <label for="telefoon"> Mobiele Telefoon</label><br>
        <input type="checkbox" id="toets" name="toets">
        <label for="toets"> Toetsenbord en Muis</label><br><br>
        Referentie: <br>
        <input type="text" id="referentie" name="referentie"><br>
        <input type="submit" value="Opslaan">
    </form>
    </div>
</body>
</html>