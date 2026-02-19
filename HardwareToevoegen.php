<?php require 'backend/hardware_toevoegen_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HardwareToevoegen.css">

    <script src="javascript/main.js" defer></script>
    <script src="javascript/HardwareToevoegen.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<header>
    <img src="img/menu.png" alt="Menu button" class="menu-button" onclick="toggleMenu()">
    <img src="img/logo.svg" alt="VDL Groep Logo">

    <a href="index.php">Homepagina</a>
    <a href="HuidigeToegangen.php">Medewerkers</a>
    <a href="HardwareToewijzen.php">Hardware</a>
    <a href="Verantwoordelijke.php">Producten</a>
    <a href="afdelingRouter.php">Taken</a>

    <div class="logout-container">
        <a href="logout.php" class="logout-button">Uitloggen</a>
    </div>
</header>

<div class="content">

    <?php if (!empty($_SESSION['melding'])): ?>
        <div class="melding"><?= $_SESSION['melding'] ?></div>
        <?php unset($_SESSION['melding']); ?>
    <?php endif; ?>

    <form method="POST">
        <h2>Hardware toevoegen</h2>

        Serienummer:<br>
        <input type="text" name="serienummer" required>

        Merk:<br>
        <input type="text" name="merk">

        Model:<br>
        <input type="text" name="model">

        Prijs:<br>
        <input type="text" name="prijs">

        Aankoopdatum:<br>
        <input type="date" name="aankoopdatum">

        <input type="submit" value="Opslaan">
    </form>

</div>
</body>
</html>
