<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once 'backend/config2.php';

$gebruikerEmail = $_SESSION['email'];
$afdelingRecht = "IT Contact";

// Check toegang
$stmt = $pdo->prepare("SELECT Bedrijf FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdelingRecht, $gebruikerEmail]);
$bedrijven = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($bedrijven)) {
    header("Location: forbidden.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interne VDL Bus & Coach portal voor hardwarebeheer, bestellingen en medewerkerstoegang.">
    <title>VDL Bus & Coach</title>
    <link rel="stylesheet" href="css/Verantwoordelijke.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="javascript/Verantwoordelijke.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'producten');
?>

<main>
<div class="content">

<?php if (count($bedrijven) > 1): ?>
    <label>
    Bedrijf:
    <input list="bedrijven" id="bedrijf" name="bedrijf" required oninput="laadProducten()">
    </label>

    <datalist id="bedrijven">
        <?php foreach ($bedrijven as $b): ?>
            <option value="<?= htmlspecialchars($b) ?>"></option>
        <?php endforeach; ?>
    </datalist>
<?php else: ?>
    <input type="hidden" id="bedrijf" name="bedrijf" value="<?= htmlspecialchars($bedrijven[0]) ?>">
    <script>
        document.addEventListener("DOMContentLoaded", () => laadProducten());
    </script>
<?php endif; ?>

    <div id="producten-container"></div>
</div>
</main>
</body>
</html>