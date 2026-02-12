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
        <form action="">
            Naam: <br>
            <input onchange="Naamingevuld()" type="text" id="naam" name="naam"><br>
            Serienummer: <br>
            <input type="text" id="serienummer" name="serienummer"><br>
            <datalist id="serienummerLijst">
                <option value="BCO11L-3171M2L"></option>
                <option value="BCO011L-3171M2C"></option>
                <option value="BCO011L-3171M2V"></option>
                <option value="BCO011L-3171M2T"></option>
            </datalist>
            Uitgiftedatum: <br>
            <input type="date" id="uitgiftedatum" name="uitgiftedatum"><br><br>
        </form>
            <button class="button" id="Opslaan">Opslaan</button>
            <button class="button" id="zoek" onclick="naamzoeken()">Zoek</button>
            <a href="HardwareToevoegen" class="button">Hardware Toevoegen</a>
<br><br><br>
        <table id="Toegewezen">
            <tr>
                <th>Naam</th>
                <th>Serienummer</th>
                <th>Uitgiftedatum</th>
                <th>Verwijderen</th>
            </tr>
            <tr>
                <td>Chrisjan van Houtert</td>
                <td>BCO11L-3171M2L</td>
                <td>09-02-2025</td>
                <td><button class="button">Verwijderen</button></td>
            </tr>
            <tr>
                <td>Chrisjan van Houtert</td>
                <td>BCO011L-3171M2C</td>
                <td>02-02-2026</td>
                <td><button class="button">Verwijderen</button></td>
            </tr>
            <tr>
                <td>Pim Verlinden</td>
                <td>BCO011L-3171M2V</td>
                <td>02-10-2025</td>
                <td><button class="button">Verwijderen</button></td>
            </tr>
            <tr style="color: green;">
                <td>Voorraad</td>
                <td>BCO011L-3171M2T</td>
                <td>11-02-2026</td>
                <td><button class="button">Verwijderen</button></td>
            </tr>
        </table>
    </div>
</body>
</html>