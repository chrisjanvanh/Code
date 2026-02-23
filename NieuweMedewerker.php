<?php require 'backend/medewerker/nieuwe_medewerker_logic.php'; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/NieuweMedewerker.css">

    <script src="javascript/main.js" defer></script>
    <script src="javascript/NieuweMedewerker.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<header>
    <img src="img/menu.png" alt="Menu button" class="menu-button" onclick="toggleMenu()">
    <img src="img/logo.svg" alt="VDL Groep Logo">

    <a href="index.php">Homepagina</a>
    <a href="HuidigeToegangen.php" class="current">Medewerkers</a>
    <a href="HardwareToewijzen.php">Hardware</a>
    <a href="Verantwoordelijke.php">Producten</a>
    <a href="afdelingRouter.php">Taken</a>

    <div class="logout-container">
        <a href="logout.php" class="logout-button">Uitloggen</a>
    </div>
</header>

<div class="content">

    <?php if (!empty($melding)): ?>
        <div class="melding"><?= $melding ?></div>
    <?php endif; ?>

    <form method="POST">
        <h2>Nieuwe medewerker</h2>

        Naam:
        <input type="text" id="naam" name="naam" required>

        Functie:
        <input type="text" id="functie" name="functie" required>

        Locatie:
        <input type="text" id="locatie" name="locatie" required>

        Leidinggevende:
        <input type="text" id="leidinggevende" name="leidinggevende" required>

        Bedrijf:
        <input type="text" id="bedrijf" name="bedrijf" required>

        Email:
        <input type="email" id="email" name="email" required>
        <br>

        <h2>Komt de medewerker contractueel in dienst?</h2>
        <input type="radio" id="ja" name="radiogroep" value="Ja" required>
        <label for="ja">Ja</label> <br>
        <input type="radio" id="nee" name="radiogroep" value="Nee" required>
        <label for="nee">Nee</label><br><br>

        <h2>De nieuwe medewerker heeft het volgende nodig:</h2>
        <h3>Software & Hardware</h3>

        <?php foreach ($columns as $col): ?>
            <?php if (!in_array($col['Field'], $exclude)): ?>
                <input type="checkbox" id="<?= $col['Field'] ?>" name="<?= $col['Field'] ?>">
                <label for="<?= $col['Field'] ?>"><?= $col['Field'] ?></label><br>
            <?php endif; ?>
        <?php endforeach; ?>

        <br><br>

        Referentie:
        <input type="text" id="referentie" name="referentie">

        <input type="submit" value="Opslaan">
    </form>

</div>
</body>
</html>
