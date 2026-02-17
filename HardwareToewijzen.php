<?php
require 'backend/config.php';

$melding = "";

/* ---------------------------------------------------
   1. TABEL VULLEN
--------------------------------------------------- */
$result = $conn->query("SELECT * FROM Gebruikersname ORDER BY Naam ASC");
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
        } else {

            /* 2.2 Check of serienummer bestaat in Hardware */
            $checkHW = $conn->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
            $checkHW->bind_param("s", $serienummer);
            $checkHW->execute();
            $checkHW->store_result();

            if ($checkHW->num_rows == 0) {
                $melding = "Dit serienummer bestaat niet in de hardwarelijst.";
            } else {

                /* 2.3 Check of serienummer al is toegewezen */
                $checkSN = $conn->prepare("SELECT 1 FROM Gebruikersname WHERE Serienummer = ?");
                $checkSN->bind_param("s", $serienummer);
                $checkSN->execute();
                $checkSN->store_result();

                if ($checkSN->num_rows > 0) {
                    $melding = "Dit serienummer is al toegewezen.";
                } else {

                    /* 2.4 INSERT uitvoeren */
                    $stmt = $conn->prepare("
                        INSERT INTO Gebruikersname (Serienummer, Naam, Uitgiftedatum)
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

    $del = $conn->prepare("DELETE FROM Gebruikersname WHERE Serienummer = ?");
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
    <title>Stage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HardwareToewijzen.css">
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
        <script src="javascript/HardwareToewijzen.js"></script>
    </header>

    <div class="content">
        <h1>Hardware Toewijzen</h1>
        <form method="POST">
            Naam: <br>
            <input onchange="Naamingevuld()" type="text" id="naam" name="naam"><br>

            Serienummer: <br>
            <input type="text" id="serienummer" name="serienummer"><br>

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
                    <td><?= htmlspecialchars($row['Uitgiftedatum']) ?></td>
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