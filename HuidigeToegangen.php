<?php 
require 'backend/medewerker/huidige_toegangen_logic.php'; 
?>
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

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'medewerkers');
?>

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
            <br><br>
        <h2>Bestanden</h2>

        <?php if (empty($bestanden)): ?>
            <p>Geen bestanden gevonden voor deze medewerker.</p>
        <?php else: ?>
            <table class="bestanden">
                <tr>
                    <th>Bestandsnaam</th>
                    <th>Upload datum</th>
                    <th>Download</th>
                </tr>

                <?php foreach ($bestanden as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['BestandNaam']) ?></td>
                        <td><?= $b['UploadDatum'] ?></td>
                        <td>
                            <a class="button" href="backend/download.php?id=<?= $b['ID'] ?>">
                                Download
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3">
                        <form action="backend/medewerker/upload_bestand.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="email" value="<?= $medewerker['Email'] ?>">

                            <label for="upload">Upload een bestand voor <?= htmlspecialchars($medewerker['Naam']) ?></label>
                            <input type="file" id="upload" name="upload" required>

                            <button type="submit" class="button">Upload</button>
                        </form>
                    </td>
                </tr>

            </table>
        <?php endif; ?>
            <br><br>
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

        <br><br>
        <input type="button" name="verwijderen" value="Verwijderen" class="button delete" onclick="verwijderMedewerker('<?= $medewerker['Naam'] ?>')">
    <?php endif; ?>

</div>
</body>
</html>
