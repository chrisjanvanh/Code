<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require "backend/logboek.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/Logboek.css">
    <script src="javascript/main.js"></script>
    <script src="javascript/Logboek.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'logboek');
?>

    <div class="content">

    <h2>Soorten</h2>
        <input type="checkbox" id="Inloggen" name="Inloggen" value="Inloggen">
        <label for="Inloggen">Inloggen</label><br>

        <input type="checkbox" id="NieuweMedewerker" name="NieuweMedewerker" value="NieuweMedewerker">
        <label for="NieuweMedewerker">Nieuwe Medewerker</label>

        <br><br>
        <h2>Logboek</h2>
            <div class="Logboek">
    <table>
        <tr>
            <th>Actie</th>
            <th>Soort</th>
            <th>Datum en Tijd</th>
        </tr>
        <?php foreach ($actie as $a): ?>
            <tr class="logrow" data-soort="<?= $a['Soort'] ?>">
                <td><?= $a['Actie'] ?></td>
                <td><?= $a['Soort'] ?></td>
                <td><?= date("H:i:s d-m-Y", strtotime($a['Timestamp'])) ?></td>
            </tr>
        <?php endforeach ?>
    </div>
</body>
</html>