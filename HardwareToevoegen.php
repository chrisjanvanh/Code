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
toonMenu($rechten, 'home'); // 'home' vervangen door de juiste pagina
?>

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
        <input type="text" name="merk" required>

        Model:<br>
        <input type="text" name="model" required>

        Prijs:<br>
        <input type="text" name="prijs" required>

        Aankoopdatum:<br>
        <input type="date" name="aankoopdatum" required>

        <input type="submit" value="Opslaan">
    </form>

</div>
</body>
</html>
