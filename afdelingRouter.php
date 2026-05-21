<?php 
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

session_start();

require 'backend/afdeling/afdelingRouter_logic.php'; 
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interne VDL Bus & Coach portal voor hardwarebeheer, bestellingen en medewerkerstoegang.">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/afdelingRouter.css">

    <script src="javascript/main.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'taken'); 
?>

<main>
<div class="content">
    <h2>Kies jouw afdeling</h2>

    <?php foreach ($bedrijven_gegroepeerd as $bedrijf => $afdelingenLijst): ?>
        <h3><?= htmlspecialchars($bedrijf) ?></h3>

        <?php foreach ($afdelingenLijst as $afd): ?>
            <a class="afdeling-btn" 
            href="Afdeling.php?afdeling=<?= urlencode($afd) ?>&bedrijf=<?= urlencode($bedrijf) ?>">
                <?= htmlspecialchars($afd) ?>
            </a>
        <?php endforeach; ?>

        <br><br>
    <?php endforeach; ?>

</div>
</main>
</body>
</html>
