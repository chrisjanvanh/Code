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

    <h1>Logboek</h1>
    <h3>Soorten</h3>
        <input type="checkbox" id="Inloggen" name="Inloggen" value="Inloggen">
        <label for="Inloggen">Inloggen</label><br>

        <input type="checkbox" id="Nieuwe Medewerker" name="Nieuwe Medewerker" value="Nieuwe Medewerker">
        <label for="Nieuwe Medewerker">Nieuwe Medewerker</label><br>

        <input type="checkbox" id="Huidige Medewerker" name="Huidige Medewerker" value="Huidige Medewerker">
        <label for="Huidige Medewerker">Huidige Medewerker</label><br>

        <input type="checkbox" id="Nieuwe Hardware" name="Nieuwe Hardware" value="Nieuwe Hardware">
        <label for="Nieuwe Hardware">Nieuwe Hardware</label><br>

        <input type="checkbox" id="Toewijzen Hardware" name="Toewijzen Hardware" value="Toewijzen Hardware">
        <label for="Toewijzen Hardware">Nieuwe Hardware</label><br>

        <input type="checkbox" id="Producten" name="Producten" value="Producten">
        <label for="Producten">Producten</label><br>

        <input type="checkbox" id="Taken" name="Taken" value="Taken">
        <label for="Taken">Taken</label>

        <br><br>
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
            </table>
        </div>
    </div>
</body>
</html>