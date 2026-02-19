<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once "backend/config.php";

$gebruikerEmail = $_SESSION['email'];

// Haal alle afdelingen op waar deze gebruiker toegang toe heeft
$stmt = $conn->prepare("SELECT Afdeling FROM AfdelingEmails WHERE Email = ?");
$stmt->bind_param("s", $gebruikerEmail);
$stmt->execute();
$result = $stmt->get_result();

$afdelingen = [];
while ($row = $result->fetch_assoc()) {
    $afdelingen[] = $row['Afdeling'];
}

// 1. Geen afdelingen → forbidden
if (empty($afdelingen)) {
    header("Location: forbidden.php");
    exit;
}

// 2. Eén afdeling → direct doorsturen
if (count($afdelingen) === 1) {
    $afdeling = urlencode($afdelingen[0]);
    header("Location: Afdeling.php?afdeling=$afdeling");
    exit;
}

// 3. Meerdere afdelingen → toon keuzescherm
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/afdelingRouter.css">

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
        <a href="afdelingRouter.php" class="current">Taken</a>
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
        <h2>Kies jouw afdeling</h2><br><br>

        <?php foreach ($afdelingen as $afd): ?>
            <a class="afdeling-btn" href="Afdeling.php?afdeling=<?= urlencode($afd) ?>">
                <?= htmlspecialchars($afd) ?>
            </a>
        <?php endforeach; ?>
    </div>
</body>
</html>
