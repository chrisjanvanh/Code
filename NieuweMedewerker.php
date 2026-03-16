<?php 
require 'backend/medewerker/nieuwe_medewerker_logic.php'; 
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<div class="content">

    <?php if (!empty($melding)): ?>
        <div class="melding"><?= $melding ?></div>
    <?php endif; ?>

    <form method="POST">
        <h2>Nieuwe medewerker</h2>

        Naam:
        <input type="text" id="naam" name="naam" required>

        Functie:
        <input type="text" id="functie" name="functie">

        Locatie:
        <input type="text" id="locatie" name="locatie" required>

        Leidinggevende:
        <input type="text" id="leidinggevende" name="leidinggevende">

        Bedrijf:
        <input list="bedrijven" id="bedrijf" name="bedrijf" required autocomplete="organization" oninput="laadProducten()">

        <datalist id="bedrijven">
            <?php foreach ($bedrijven as $b): ?>
                <option value="<?= htmlspecialchars($b) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        Email:
        <input type="email" id="email" name="email">
        <br>

        <h2>Komt de medewerker contractueel in dienst?</h2>
        <input type="radio" id="ja" name="radiogroep" value="Ja" required>
        <label for="ja">Ja</label> <br>
        <input type="radio" id="nee" name="radiogroep" value="Nee" required>
        <label for="nee">Nee</label><br><br>

        <h2>De nieuwe medewerker heeft het volgende nodig:</h2>
        <h3>Software & Hardware</h3>

        <div id="producten-container">
            <!-- Wordt gevuld via JavaScript -->
        </div>

        <br><br>

        Referentie:
        <input type="text" id="referentie" name="referentie">

        <input type="submit" value="Opslaan">
    </form>

</div>
</body>
</html>
