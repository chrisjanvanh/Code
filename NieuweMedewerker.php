<?php 
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

        Naam:
        <input type="text" id="naam" name="naam" required autocomplete="off">

        Functie:
        <input type="text" id="functie" name="functie" autocomplete="off">

        Locatie:
        <input type="text" id="locatie" name="locatie" required autocomplete="off">

        Leidinggevende:
        <input type="text" id="leidinggevende" name="leidinggevende" autocomplete="off">

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


        Email:
        <input type="email" id="email" name="email" autocomplete="off">
        <br>

        <h2>Komt de medewerker contractueel in dienst?</h2>
        <input type="radio" id="ja" name="radiogroep" value="Ja" required>
        <label for="ja">Ja</label> <br>
        <input type="radio" id="nee" name="radiogroep" value="Nee" required>
        <label for="nee">Nee</label><br><br>

        <div id="producten-container"></div>

        <br><br>

        Referentie:
        <input type="text" id="referentie" name="referentie" autocomplete="off">

        <input type="submit" value="Opslaan">
    </form>

</div>
</main>
</body>
</html>
