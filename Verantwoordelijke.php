<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once 'backend/config2.php';

$gebruikerEmail = $_SESSION['email'];
$afdelingRecht = "Business IT";

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

<div class="content">

<?php if (count($bedrijven) > 1): ?>
    Bedrijf:
    <input list="bedrijven" id="bedrijf" name="bedrijf" required oninput="laadProducten()">

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
</body>
</html>
