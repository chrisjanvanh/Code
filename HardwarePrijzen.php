<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require 'backend/HardwareBestellen_logic.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HardwareBestellen.css">
    <script src="javascript/main.js"></script>
    <script src="javascript/HardwareBestellen.js"></script>
    <script src="javascript/HardwarePrijzen.js"></script>

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

        <form method="POST" action="backend/bestelling_verwerken.php">
            <?php if (count($bedrijven) > 1): ?>
                Bedrijf:
                <input list="bedrijven" id="bedrijf" name="bedrijf" required>

                <datalist id="bedrijven">
                    <?php foreach ($bedrijven as $b): ?>
                        <option value="<?= htmlspecialchars($b) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            <?php else: ?>
                <input type="hidden" id="bedrijf" name="bedrijf" value="<?= htmlspecialchars($bedrijven[0]) ?>">
            <?php endif; ?>
            
            <h3>Aanvrager:</h3>
            Naam:
            <input type="text" id="naamAanvrager" name="naamAanvrager" required>

            Datum:
            <input type="date" id="datumAanvrager" name="datumAanvrager" required>

            Telefoonnummer:
            <input type="tel" id="telefoonAanvrager" name="telefoonAanvrager" pattern="^(?:06\s?\d{8}|\+31\s?6\s?\d{8})$" required>

            t.l.v. investeringsnummer:
            <input type="text" id="investeringsnummer" name="investeringsnummer">

            <h3>Voor:</h3>
            Naam:
            <input type="text" id="naam" name="naam" required>
            Afdeling:
            <input type="text" id="afdeling" name="afdeling" required>
            Telefoonnummer:
            <input type="tel" id="telefoon" name="telefoon" pattern="^(?:06\s?\d{8}|\+31\s?6\s?\d{8})$" required>
            Bestaande gebruiker: <br>
            <label class="switch">
                <input type="checkbox" name="bestaande_gebruiker" value="ja">
                <span class="slider round"></span>
            </label> <br><br>
            Huidige computernaam indien van toepassing:
            <input type="text" id="huidig" name="huidige_computernaam">

            <h3>Motivatie</h3>
            <input type="text" id="motivatie" name="motivatie">
            <br>

            <table>
                <tr>
                    <th>Product</th>
                    <th>Vervanging aantal</th>
                    <th>Uitbreiding aantal</th>
                    <th>Prijs/stuk</th>
                    <th>Totaalprijs</th>
                </tr>
                <?php foreach ($producten as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['Naam']) ?></td>

                        <!-- Vervanging -->
                        <td>
                            <input type="number" id="<?= $p['ID'] ?>1" name="<?= $p['ID'] ?>1">
                        </td>

                        <!-- Uitbreiding -->
                        <td>
                            <input type="number" id="<?= $p['ID'] ?>2" name="<?= $p['ID'] ?>2">
                        </td>

                        <!-- Prijs -->
                        <td>
                            € <input 
                                type="text"
                                name="prijs_<?= $p['ID'] ?>"
                                value="<?= number_format($p['Prijs'], 2, ',', '.') ?>"
                                onkeydown="if(event.key === 'Enter'){ updatePrijs(<?= $p['ID'] ?>, this.value); event.preventDefault(); }"
                            >
                        </td>



                        <!-- Totaalprijs (leeg, wordt door JS gevuld) -->
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="height: 50px;">
                    <td colspan="4" style="text-align: right; font-weight: bold;">Eindtotaal:</td>
                    <td id="eindtotaal"></td>
                </tr>
            </table><br>
            <input type="submit" value="Versturen">
        </form>
    </div>
</body>
</html>