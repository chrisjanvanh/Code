<?php 
require 'backend/afdeling/afdelingRouter_logic.php'; 
?>
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

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'home'); // 'home' vervangen door de juiste pagina
?>

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
