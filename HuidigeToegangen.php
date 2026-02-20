<?php require 'backend/medewerker/huidige_toegangen_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HuidigeToegangen.css">

    <script src="javascript/main.js" defer></script>
    <script src="javascript/HuidigeToegang.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<header>
    <img src="img/menu.png" alt="Menu button" class="menu-button" onclick="toggleMenu()">
    <img src="img/logo.svg" alt="VDL Groep Logo">

    <a href="index.php">Homepagina</a>
    <a href="HuidigeToegangen.php" class="current">Medewerkers</a>
    <a href="HardwareToewijzen.php">Hardware</a>
    <a href="Verantwoordelijke.php">Producten</a>
    <a href="afdelingRouter.php">Taken</a>

    <div class="logout-container">
        <a href="logout.php" class="logout-button">Uitloggen</a>
    </div>
</header>

<div class="content">

    <?php if (!empty($_SESSION['melding'])): ?>
        <div class="melding">
            <?= $_SESSION['melding'] ?>
        </div>
        <?php unset($_SESSION['melding']); ?>
    <?php endif; ?>

    <h2>Medewerker zoeken</h2>

    <form method="GET">
        Naam: <br>
        <input list="namen" id="naam" name="naam" required>

        <datalist id="namen">
            <?php foreach ($medewerkers as $m): ?>
                <option value="<?= htmlspecialchars($m['Naam']) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        <button type="submit" class="button">Zoeken</button>
        <a href="NieuweMedewerker.php" class="button">Medewerker Toevoegen</a>
    </form>

    <?php if ($medewerker): ?>

        <form method="POST">
            Functie:<br>
            <input type="text" name="functie" value="<?= $medewerker['Functie'] ?>"><br>

            Locatie:<br>
            <input type="text" name="locatie" value="<?= $medewerker['Locatie'] ?>"><br>

            Leidinggevende:<br>
            <input type="text" name="leidinggevende" value="<?= $medewerker['Leidinggevende'] ?>"><br>

            Bedrijf:<br>
            <input type="text" name="bedrijf" value="<?= $medewerker['Bedrijf'] ?>"><br>

            Email:<br>
            <input type="email" name="email" value="<?= $medewerker['Email'] ?>"><br>

            <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">

            <input type="submit" value="Opslaan">
        </form>

        <h2>Huidige toegangen</h2>

        <table>
            <tr>
                <th>Product</th>
                <th>Toevoegen</th>
                <th>Verwijderen</th>
            </tr>

            <?php foreach ($columns as $col): ?>
                <?php
                    $kolom = $col['Field'];
                    if (in_array($kolom, $exclude)) continue;

                    $waarde = $medewerker[$kolom];

                    $kleur = "white";
                    $tekst = "black";

                    if ($waarde === 0) { $kleur = "orange"; }
                    if ($waarde === 1) { $kleur = "green"; }
                    if ($waarde === 2) { $kleur = "lightgray"; $tekst = "white"; }
                ?>
                <tr>
                    <td style="background-color: <?= $kleur ?>; color: <?= $tekst ?>"><?= $kolom ?></td>

                    <td>
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze toegang wilt toevoegen?')">
                            <input type="hidden" name="actie" value="toevoegen">
                            <input type="hidden" name="veld" value="<?= $kolom ?>">
                            <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">
                            <button type="submit" class="Toevoegen">Toevoegen</button>
                        </form>
                    </td>

                    <td>
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze toegang wilt verwijderen?')">
                            <input type="hidden" name="actie" value="verwijderen">
                            <input type="hidden" name="veld" value="<?= $kolom ?>">
                            <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">
                            <button type="submit" class="Verwijderen">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php endif; ?>

</div>
</body>
</html>
