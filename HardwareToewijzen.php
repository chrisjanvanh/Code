<?php
require 'backend/config.php';

$melding = "";

// Ophalen van alle toegewezen hardware
$result = $conn->query("SELECT * FROM Gebruiksname ORDER BY Naam ASC");
$toegewezen = $result->fetch_all(MYSQLI_ASSOC);

// Opslaan van nieuwe toewijzing
if (isset($_POST['opslaan'])) {

    $naam = $_POST['naam'];
    $serienummer = $_POST['serienummer'];
    $uitgiftedatum = $_POST['uitgiftedatum'];

    // Check of medewerker bestaat
    $checkNaam = $conn->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
    $checkNaam->bind_param("s", $naam);
    $checkNaam->execute();
    $checkNaam->store_result();

    if ($checkNaam->num_rows == 0) {
        $melding = "Deze medewerker bestaat niet.";
    } else {

        // Check of serienummer al bestaat
        $checkSN = $conn->prepare("SELECT 1 FROM Gebruiksname WHERE Serienummer = ?");
        $checkSN->bind_param("s", $serienummer);
        $checkSN->execute();
        $checkSN->store_result();

        if ($checkSN->num_rows > 0) {
            $melding = "Dit serienummer is al toegewezen.";
        } else {

            // INSERT uitvoeren
            $stmt = $conn->prepare("INSERT INTO Gebruiksname (Serienummer, Naam, Uitgiftedatum) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $serienummer, $naam, $uitgiftedatum);

            if ($stmt->execute()) {
                $melding = "Hardware succesvol toegewezen!";
                header("Refresh:0"); // pagina herladen zodat tabel update
            } else {
                $melding = "Er is iets fout gegaan.";
            }
        }
    }
}

// Verwijderen
if (isset($_POST['verwijder'])) {
    $sn = $_POST['verwijder'];

    $del = $conn->prepare("DELETE FROM Gebruiksname WHERE Serienummer = ?");
    $del->bind_param("s", $sn);
    $del->execute();

    header("Refresh:0");
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