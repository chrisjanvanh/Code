<?php 
require 'backend/medewerker/huidige_toegangen_logic.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interne VDL Bus & Coach portal voor hardwarebeheer, bestellingen en medewerkerstoegang.">
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
<main>
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
        <input list="namen" id="naam" name="naam" required autocomplete="off">

        <datalist id="namen">
            <?php foreach ($medewerkers as $m): ?>
                <option value="<?= htmlspecialchars($m['Naam']) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        <button type="submit" class="button">Zoeken</button>
        <a href="NieuweMedewerker.php" class="button">Medewerker Toevoegen</a>
    </form>

    <?php if ($medewerker): ?>
        <br><br>
        <form method="POST">
            Functie:<br>
            <input type="text" name="functie" value="<?= $medewerker['Functie'] ?>" autocomplete="off"><br>

            Locatie:<br>
            <input type="text" name="locatie" value="<?= $medewerker['Locatie'] ?>"><br>

            Leidinggevende:<br>
            <input type="text" name="leidinggevende" value="<?= $medewerker['Leidinggevende'] ?>"><br>

            Bedrijf:<br>
            <input type="text" name="bedrijf" value="<?= $medewerker['Bedrijf'] ?>"><br>

            Email:<br>
            <input type="email" name="email" value="<?= $medewerker['Email'] ?>" autocomplete="off"><br>

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
                    <th>Verwijderen</th>
                </tr>

                <?php foreach ($bestanden as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['BestandNaam']) ?></td>
                        <td><?= date("d-m-Y",strtotime($b['UploadDatum'])) ?></td>
                        <td>
                            <a class="button" href="backend/download.php?id=<?= $b['ID'] ?>">
                                Download
                            </a>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Weet je zeker dat je dit bestand wilt verwijderen?')">
                                <input type="hidden" name="actie" value="verwijderen_bestand">
                                <input type="hidden" name="bestand_id" value="<?= $b['ID'] ?>">
                                <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">
                                <button type="submit" class="button delete">Verwijderen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4">
                        <form action="backend/medewerker/upload_bestand.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="email" value="<?= $medewerker['Email'] ?>">
                            <input type="hidden" name="naam" value="<?= $medewerker['Naam'] ?>">

                            <label for="upload">Upload een bestand </label>
                            <input type="file" id="upload" name="upload" required>

                            <button type="submit" class="button">Verstuur</button>
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
                <th>Status</th>
                <th>Toevoegen</th>
                <th>Verwijderen</th>
            </tr>

            <?php foreach ($toegangen as $t): ?>
                <?php
                    $product = $t['Product'];
                    $waarde  = $t['Waarde'];

                    // Kleuren
                    $kleur = "white";
                    $tekst = "black";

                    if ($waarde === 0) { $kleur = "orange"; }
                    if ($waarde === 1) { $kleur = "green"; }
                    if ($waarde === 2) { $kleur = "lightgray"; $tekst = "white"; }
                    if ($waarde === 3) { $kleur = "yellow"; }
                ?>
                <tr>
                    <td><?= htmlspecialchars($product) ?></td>

                    <td style="background-color: <?= $kleur ?>; color: <?= $tekst ?>">
                        <?php
                            if ($waarde === null) echo "Geen toegang";
                            if ($waarde === 0)    echo "Aangevraagd";
                            if ($waarde === 1)    echo "Toegekend";
                            if ($waarde === 2)    echo "Verwijderd";
                            if ($waarde === 3)    echo "In wachtrij";
                        ?>
                    </td>

                    <td>
                        <?php if ($waarde === null): ?>
                            <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze toegang wilt toevoegen?')">
                                <input type="hidden" name="actie" value="toevoegen">
                                <input type="hidden" name="veld" value="<?= htmlspecialchars($product) ?>">
                                <input type="hidden" name="naam" value="<?= htmlspecialchars($medewerker['Naam']) ?>">
                                <button type="submit" class="Toevoegen">Toevoegen</button>
                            </form>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($waarde === 1): ?>
                            <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze toegang wilt verwijderen?')">
                                <input type="hidden" name="actie" value="verwijderen">
                                <input type="hidden" name="veld" value="<?= htmlspecialchars($product) ?>">
                                <input type="hidden" name="naam" value="<?= htmlspecialchars($medewerker['Naam']) ?>">
                                <button type="submit" class="Verwijderen">Verwijderen</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>


        <br><br>
        <input type="button" 
       name="verwijderen" 
       value="Verwijderen" 
       class="button medewerkerverwijderen" 
       onclick="verwijderMedewerker('<?= $medewerker['Naam'] ?>', '<?= $medewerker['Bedrijf'] ?>')">

    <?php endif; ?>

</div>
</main>
</body>
</html>
