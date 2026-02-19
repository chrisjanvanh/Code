<?php require 'backend/afdeling/afdelingRouter_logic.php'; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/afdelingRouter.css">

    <script src="javascript/main.js" defer></script>

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
    <a href="afdelingRouter.php" class="current">Taken</a>

    <div class="logout-container">
        <a href="logout.php" class="logout-button">Uitloggen</a>
    </div>
</header>

<div class="content">
    <h2>Kies jouw afdeling</h2>

    <?php foreach ($afdelingen as $afd): ?>
        <a class="afdeling-btn" href="Afdeling.php?afdeling=<?= urlencode($afd) ?>">
            <?= htmlspecialchars($afd) ?>
        </a>
    <?php endforeach; ?>
</div>

</body>
</html>
