<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require 'backend/config.php';

$medewerkers = $conn->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC")->fetch_all(MYSQLI_ASSOC);
$hardware = $conn->query("SELECT Serienummer FROM Hardware ORDER BY Serienummer ASC")->fetch_all(MYSQLI_ASSOC);

$melding = "";

/* ---------------------------------------------------
   1. TABEL VULLEN
--------------------------------------------------- */
$result = $conn->query("SELECT * FROM Gebruikname ORDER BY uitgiftedatum DESC");
$toegewezen = $result->fetch_all(MYSQLI_ASSOC);

/* ---------------------------------------------------
   2. OPSLAAN VAN NIEUWE TOEWĲZING
--------------------------------------------------- */
if (isset($_POST['opslaan'])) {

    $naam = trim($_POST['naam']);
    $serienummer = trim($_POST['serienummer']);
    $uitgiftedatum = $_POST['uitgiftedatum'];

    // Lege velden checken
    if ($naam === "" || $serienummer === "" || $uitgiftedatum === "") {
        $melding = "Vul alle velden in.";
    } else {

        /* 2.1 Check of medewerker bestaat */
        $checkNaam = $conn->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
        $checkNaam->bind_param("s", $naam);
        $checkNaam->execute();
        $checkNaam->store_result();

        if ($checkNaam->num_rows == 0) {
            $melding = "Deze medewerker bestaat niet.";
            echo "<script>alert('$melding');</script>";
        } else {

            /* 2.2 Check of serienummer bestaat in Hardware */
            $checkHW = $conn->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
            $checkHW->bind_param("s", $serienummer);
            $checkHW->execute();
            $checkHW->store_result();

            if ($checkHW->num_rows == 0) {
                $melding = "Dit serienummer bestaat niet in de hardwarelijst.";
                echo "<script>alert('$melding');</script>";
            } else {

                /* 2.3 Check of serienummer al is toegewezen */
                $checkSN = $conn->prepare("SELECT 1 FROM Gebruikname WHERE Serienummer = ?");
                $checkSN->bind_param("s", $serienummer);
                $checkSN->execute();
                $checkSN->store_result();

                if ($checkSN->num_rows > 0) {
                    $melding = "Dit serienummer is al toegewezen.";
                    echo "<script>alert('$melding');</script>";
                } else {

                    /* 2.4 INSERT uitvoeren */
                    $stmt = $conn->prepare("
                        INSERT INTO Gebruikname (Serienummer, Naam, Uitgiftedatum)
                        VALUES (?, ?, ?)
                    ");
                    $stmt->bind_param("sss", $serienummer, $naam, $uitgiftedatum);

                    if ($stmt->execute()) {
                        $melding = "Hardware succesvol toegewezen!";
                        header("Refresh:0");
                        exit;
                    } else {
                        error_log("Insert error: " . $stmt->error, 3, __DIR__ . "/error.log");
                        $melding = "Er is iets fout gegaan, probeer het later opnieuw.";
                        echo "<script>alert('$melding');</script>";
                    }
                }
            }
        }
    }
}

/* ---------------------------------------------------
   3. VERWIJDEREN
--------------------------------------------------- */
if (isset($_POST['verwijder'])) {

    $sn = $_POST['verwijder'];

    $del = $conn->prepare("DELETE FROM Gebruikname WHERE Serienummer = ?");
    $del->bind_param("s", $sn);
    $del->execute();

    header("Refresh:0");
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
    <link rel="stylesheet" href="css/HardwareToewijzen.css">
    <script src="javascript/main.js"></script>
    <script src="javascript/HardwareToewijzen.js"></script>

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

        <h1>Hardware Toewijzen</h1>
        <form method="POST">
            Naam: <br>
            <input list="namen" id="naam" name="naam" required>

            <datalist id="namen">
                <?php foreach ($medewerkers as $m): ?>
                    <option value="<?= htmlspecialchars($m['Naam']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <br>

            Serienummer: <br>
            <input list="serienummers" id="serienummer" name="serienummer" required>

            <datalist id="serienummers">
                <?php foreach ($hardware as $h): ?>
                    <option value="<?= htmlspecialchars($h['Serienummer']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <br>

            Uitgiftedatum: <br>
            <input type="date" id="uitgiftedatum" name="uitgiftedatum"><br><br>

            <button class="button" name="opslaan">Opslaan</button>
        </form>

            <button type="button" class="button" id="zoek" onclick="naamzoeken()">Zoek</button>
            <a href="HardwareToevoegen.php" class="button">Hardware Toevoegen</a>
<br><br><br>
                <table id="Toegewezen">
            <tr>
                <th>Naam</th>
                <th>Serienummer</th>
                <th>Uitgiftedatum</th>
                <th>Verwijderen</th>
            </tr>

            <?php foreach ($toegewezen as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Naam']) ?></td>
                    <td><?= htmlspecialchars($row['Serienummer']) ?></td>
                    <td><?= date("d-m-Y", strtotime($row['Uitgiftedatum'])) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je dit wilt verwijderen?')">
                            <button class="button" name="verwijder" value="<?= $row['Serienummer'] ?>">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>