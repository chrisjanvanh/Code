<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require 'backend/config.php';

$medewerkers = $conn->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC")->fetch_all(MYSQLI_ASSOC);

$melding = "";

// Haal alle kolommen op van de tabel Medewerker
$columnsResult = $conn->query("SHOW COLUMNS FROM Medewerker");
$columns = $columnsResult->fetch_all(MYSQLI_ASSOC);

// Kolommen die GEEN toegang zijn
$exclude = ["Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf", "Referentie"];

// Medewerker ophalen
$medewerker = null;

if (isset($_GET['naam'])) {
    $naam = $_GET['naam'];

    $stmt = $conn->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
    $stmt->bind_param("s", $naam);
    $stmt->execute();
    $result = $stmt->get_result();
    $medewerker = $result->fetch_assoc();
}

/* ---------------------------------------------------
   1. Toevoegen / Verwijderen van toegang
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actie'])) {

    $veld = $_POST['veld'];
    $naam = $_POST['naam'];

    if ($_POST['actie'] === "toevoegen") {
        $waarde = 0;
    } else {
        $waarde = 2;
    }

    $stmt = $conn->prepare("UPDATE Medewerker SET `$veld` = ? WHERE Naam = ?");
    $stmt->bind_param("is", $waarde, $naam);
    $stmt->execute();

    header("Location: HuidigeToegangen.php?naam=" . urlencode($naam));
    exit;
}

/* ---------------------------------------------------
   2. Opslaan van bovenste velden
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['functie']) && !isset($_POST['actie'])) {

    $stmt = $conn->prepare("
        UPDATE Medewerker 
        SET Functie=?, Locatie=?, Leidinggevende=?, Bedrijf=? 
        WHERE Naam=?
    ");
    $stmt->bind_param(
        "sssss",
        $_POST['functie'],
        $_POST['locatie'],
        $_POST['leidinggevende'],
        $_POST['bedrijf'],
        $_POST['naam']
    );
    $stmt->execute();

    $melding = "Gegevens succesvol opgeslagen!";
    echo "<script>alert('$melding');</script>";

    header("Location: HuidigeToegangen.php?naam=" . urlencode($_POST['naam']));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HuidigeToegangen.css">
    <script src="javascript/HuidigeToegang.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
<header>
    <img src="img/menu.png" alt="Menu button" class="menu-button" set onclick="toggleMenu()">
    <img src="img/logo.svg" alt="VDL Groep Logo">
    <a href="index.php">Homepagina</a>
    <a href="HuidigeToegangen.php" class="current">Medewerkers</a>
    <a href="HardwareToewijzen.php"> Hardware</a>
    <a href="Verantwoordelijke.php">Producten</a>
    <div style="
            position: absolute;
            right: 20px;
            top: 15px;
        ">
            <a href="logout.php" 
            style="padding: 8px 15px; background: #e74c3c; color: white; 
                    border-radius: 5px; text-decoration: none;">
                Uitloggen
            </a>
        </div>

</header>

<div class="content">

    <?php if (!empty($melding)): ?>
        <div class="melding">
            <?= $melding ?>
        </div>
    <?php endif; ?>

    <h2>Medewerker zoeken</h2>
    <form method="GET">
        Naam: <br>
            <input list="namen" id="naam" name="naam" required>

            <datalist id="namen">
                <?php foreach ($medewerkers as $m): ?>
                    <option value="<?= htmlspecialchars($m['Naam']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <br>
        <button type="submit" class="button">Zoeken</button>
        <a href="NieuweMedewerker.php" class="button" onclick="naamtoevoegen();">Medewerker Toevoegen</a><br><br>
    </form>

    <?php if ($medewerker): ?>

    <form method="POST">
        Functie: <br>
        <input type="text" id="functie" name="functie" value="<?= $medewerker['Functie'] ?>"><br>
        Locatie: <br>
        <input type="text" id="locatie" name="locatie" value="<?= $medewerker['Locatie'] ?>"><br>
        Leidinggevende: <br>
        <input type="text" id="leidinggevende" name="leidinggevende" value="<?= $medewerker['Leidinggevende'] ?>"><br>
        Bedrijf: <br>
        <input type="text" id="bedrijf" name="bedrijf" value="<?= $medewerker['Bedrijf'] ?>"><br>
        <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">
        <input type="submit" value="Opslaan">
    </form>

    <h2>Huidige toegangen</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Toevoegen</th>
            <th>Verwijderen</th>
        </tr>

        <?php foreach ($columns as $col): ?>
            <?php
                $kolom = $col['Field'];
                if (in_array($kolom, $exclude)) continue;

                $waarde = $medewerker[$kolom];

                // kleur bepalen
                // achtergrondkleur bepalen
                $kleur = "white";
                $tekst = "black";

                if ($waarde === 0) {
                    $kleur = "orange";
                    $tekst = "black";
                }

                if ($waarde === 1) {
                    $kleur = "green";
                    $tekst = "black";
                }

                if ($waarde === 2) {
                    $kleur = "black";
                    $tekst = "white";
                }


                // label netjes maken
                $label = $kolom;
            ?>
            <tr>
                <td style="background-color: <?= $kleur ?>; color: <?= $tekst ?>"><?= $label ?></td>

                <td>
                    <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze toegang wilt toevoegen?')">
                        <input type="hidden" name="actie" value="toevoegen">
                        <input type="hidden" name="veld" value="<?= $kolom ?>">
                        <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">
                        <button type="submit" class="Toevoegen">Toevoegen</button>
                    </form>
                </td>

                <td>
                    <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze toegang wilt verwijderen?')">
                        <input type="hidden" name="actie" value="verwijderen">
                        <input type="hidden" name="veld" value="<?= $kolom ?>">
                        <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">
                        <button type="submit" class="Verwijderen">Verwijderen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>

    <?php endif; ?>

</div>
</body>
</html>
