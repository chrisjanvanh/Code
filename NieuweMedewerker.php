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

require 'backend/medewerker/nieuwe_medewerker_logic.php'; 
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interne VDL Bus & Coach portal voor hardwarebeheer, bestellingen en medewerkerstoegang.">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/NieuweMedewerker.css">

    <script src="javascript/main.js" defer></script>
    <script src="javascript/NieuweMedewerker.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'medewerkers');
?>

<main>
<div class="content">

    <?php if (!empty($melding)): ?>
        <div class="melding"><?= $melding ?></div>
    <?php endif; ?>

    <form method="POST">
        <h2>Nieuwe medewerker</h2>

        <label>
        Naam:
        <input type="text" id="naam" name="naam" required autocomplete="off">
        </label>

        <label>
        Functie:
        <input type="text" id="functie" name="functie" autocomplete="off">
        </label>

        <label>
        Locatie:
        <input type="text" id="locatie" name="locatie" required autocomplete="off">
        </label>

        <label>
        Leidinggevende:
        <input type="text" id="leidinggevende" name="leidinggevende" autocomplete="off">
        </label>

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

        <label>
        Email:
        <input type="email" id="email" name="email" autocomplete="off">
        </label>

        <label>
        Startdatum:
        <input type="date" id="startdatum" name="startdatum">
        </label>
        <br><br>

        <h2>Komt de medewerker contractueel in dienst?</h2>
        <input type="radio" id="ja" name="radiogroep" value="Ja" required>
        <label for="ja">Ja</label> <br>
        <input type="radio" id="nee" name="radiogroep" value="Nee" required>
        <label for="nee">Nee</label><br><br>

        <div id="producten-container"></div>

        <br><br>

        <label>
        Referentie:
        <input type="text" id="referentie" name="referentie" autocomplete="off">
        </label>

        <input type="submit" value="Opslaan">
    </form>

</div>
</main>
</body>
</html>
