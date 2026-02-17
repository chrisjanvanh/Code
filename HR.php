<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HR.css">
    <script src="javascript/auth.js"></script>
    <script src="javascript/HR.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
    <header>
        <img src="img/menu.png" alt="Menu button" class="menu-button" set onclick="toggleMenu()">
        <img src="img/logo.svg" alt="VDL Groep Logo">
        <a href="index.php">Homepagina</a>
        <a href="HuidigeToegangen.php">Medewerkers</a>
        <a href="HardwareToewijzen.php"> Hardware</a>
        <a href="Verantwoordelijke.php">Producten</a>
        <a href="HR.php" class="No-Mobile" class="current">HR</a>
    </header>

    <div class="content">
        <h1>Taken</h1>
        <table>
                <tr>
                <th>Product</th>
                <th>Medewerker</th>
                <th>Functie</th>
                <th>Locatie</th>
                <th>Leidinggevende</th>
                <th>Bedrijf</th>
                <th>Referentie</th>
                <th>Taak afronden</th>
            </tr>
            <tr>
                <td>MyVDL</td>
                <td>Chrisjan van Houtert</td>
                <td>Stagair</td>
                <td>Valkenswaard</td>
                <td>Pim Verlinden</td>
                <td>VDL Bus &amp; Coach</td>
                <td>Voorganger</td>
                <td><button class="button" onclick="confirmAfronden()">Afronden</button></td>
            </tr>
            <tr>
                <td>MyVDL</td>
                <td>Pim Verlinden</td>
                <td>Manager Business IT</td>
                <td>Valkenswaard</td>
                <td>Dennis van Opzeeland</td>
                <td>VDL Bus &amp; Coach</td>
                <td>Voorganger</td>
                <td><button class="button" onclick="confirmAfronden()">Afronden</button></td>
            </tr>
        </table>
    </div>
</body>
</html>