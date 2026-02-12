<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HardwareToevoegen.css">
    <script src="javascript/auth.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
    <header>
        <img src="img/logo.svg" alt="VDL Groep Logo">
        <a href="index.php">Homepagina</a>
        <a href="HuidigeToegangen.php">Medewerkers</a>
        <a href="HardwareToewijzen.php" class="current"> Hardware</a>
        <a href="Verantwoordelijke.php">Producten</a>
    </header>

    <div class="content">
    <form action="">
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