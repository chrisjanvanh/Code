<?php 
require 'backend/hardware_toevoegen_logic.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HardwareToevoegen.css">

    <script src="javascript/main.js" defer></script>
    <!-- <script src="javascript/HardwareToevoegen.js" defer></script> -->

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'hardware');
?>

<div class="content">

    <?php if (!empty($_SESSION['melding'])): ?>
        <div class="melding"><?= $_SESSION['melding'] ?></div>
        <?php unset($_SESSION['melding']); ?>
    <?php endif; ?>

    <?php if (count($bedrijven) > 1): ?>
        <label>Bedrijf:</label><br>
        <select name="bedrijf" required>
            <option value="">-- Kies bedrijf --</option>
            <?php foreach ($bedrijven as $b): ?>
                <option value="<?= htmlspecialchars($b) ?>"
                    <?= ($geselecteerdBedrijf === $b ? "selected" : "") ?>>
                    <?= htmlspecialchars($b) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
    <?php else: ?>
        <input type="hidden" name="bedrijf" value="<?= htmlspecialchars($bedrijven[0]) ?>">
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
