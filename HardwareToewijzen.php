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

require 'backend/hardware_toewijzen_logic.php'; 
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interne VDL Bus & Coach portal voor hardwarebeheer, bestellingen en medewerkerstoegang.">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HardwareToewijzen.css">

    <script src="javascript/main.js" defer></script>
    <script src="javascript/HardwareToewijzen.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'hardware');
?>

<main>
<div class="content">

    <?php if (!empty($_SESSION['melding'])): ?>
        <div class="melding"><?= $_SESSION['melding'] ?></div>
        <?php unset($_SESSION['melding']); ?>
    <?php endif; ?>

    <?php if (!empty($melding)): ?>
        <div class="melding"><?= $melding ?></div>
    <?php endif; ?>

    <h1>Hardware Toewijzen</h1>

    <form method="GET">
        <?php if (count($bedrijven) > 1): ?>
            <label>
            Bedrijf:<br>

            <input list="bedrijven" name="bedrijf" id="bedrijf"
                value="<?= htmlspecialchars($bedrijf) ?>"
                oninput="this.form.submit()" required>
            </label>

            <datalist id="bedrijven">
                <?php foreach ($bedrijven as $b): ?>
                    <option value="<?= htmlspecialchars($b) ?>"></option>
                <?php endforeach; ?>
            </datalist>

        <?php else: ?>
            <input type="hidden" name="bedrijf" value="<?= htmlspecialchars($bedrijven[0]) ?>">
        <?php endif; ?>
    </form>

    <form method="POST">
        <input type="hidden" name="bedrijf" value="<?= htmlspecialchars($bedrijf) ?>">

        <label>
        Naam:<br>
        <input list="namen" id="naam" name="naam" required>
        </label>
        <datalist id="namen">
            <?php foreach ($medewerkers as $m): ?>
                <option value="<?= htmlspecialchars($m['Naam']) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        <label>
        Serienummer:<br>
        <input list="serienummers" id="serienummer" name="serienummer" required>
        </label>
        <datalist id="serienummers">
            <?php foreach ($alle_regels as $h): ?>
                <option value="<?= htmlspecialchars($h['Serienummer']) ?>">
                    (<?= $h['type'] === 'voorraad' ? 'Voorraad' : 'Toegewezen' ?>)
                </option>
            <?php endforeach; ?>
        </datalist>

        <label>
        Uitgiftedatum:<br>
        <input type="date" id="uitgiftedatum" name="uitgiftedatum">
        </label>

        <button class="button" name="opslaan">Opslaan</button>
        <button type="button" class="button" onclick="naamzoeken()">Zoeken</button>
        <a href="HardwareToevoegen.php" class="button">Hardware Toevoegen</a>
        <a href="GeschiedenisHardware.php" class="button">Geschiedenis</a>
    </form>

    <br><br>
    <h2>Toegewezen Hardware</h2>
    <table id="Toegewezen">
        <tr>
            <th>Naam</th>
            <th>Serienummer</th>
            <th>Model</th>
            <th>Uitgiftedatum</th>
            <th>Verwijderen</th>
        </tr>

        <?php foreach ($alle_regels as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Naam']) ?></td>
                <td><?= htmlspecialchars($row['Serienummer']) ?></td>
                <td><?= htmlspecialchars($row['Model']) ?></td>
                <td>
                    <?= $row['Uitgiftedatum'] ? date("d-m-Y", strtotime($row['Uitgiftedatum'])) : '-' ?>
                </td>
                <td>
                    <form method="POST" onsubmit="return confirm('Weet je zeker dat je dit wilt verwijderen?')">
                        <input type="hidden" name="type" value="<?= $row['type'] ?>">
                        <button class="button" name="verwijder" value="<?= $row['Serienummer'] ?>">Verwijderen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br><br>

    <?php if (!empty($bedrijf)): ?>
        <form method="get" action="backend/export_hardware_toewijzen.php" style="margin-bottom:16px;">
            <input type="hidden" name="bedrijf" value="<?= htmlspecialchars($bedrijf) ?>">
            <button type="submit" class="button">Export naar Excel</button>
        </form>
    <?php endif; ?>

</div>
</main>
</body>
</html>
