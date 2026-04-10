<?php 
require 'backend/GeschiedenisHardware_logic.php'; 
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
    <script src="javascript/GeschiedenisHardware.js" defer></script>

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

    <h1>Hardware Geschiedenis</h1>

        Naam:<br>
        <input list="namen" id="naam" name="naam" required>
        <datalist id="namen">
            <?php foreach ($medewerkers as $m): ?>
                <option value="<?= htmlspecialchars($m['Naam']) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        Serienummer:<br>
        <input list="serienummers" id="serienummer" name="serienummer" required>
        <datalist id="serienummers">
            <?php foreach ($hardware as $h): ?>
                <option value="<?= htmlspecialchars($h['Serienummer']) ?>"></option>
            <?php endforeach; ?>
        </datalist>

    <button type="button" class="button" onclick="naamzoeken()">Zoek</button>

    <table id="Toegewezen">
        <tr>
            <th>Naam</th>
            <th>Serienummer</th>
            <th>Uitgiftedatum</th>
            <th>Einddatum</th>
            <th>Bedrijf</th>
        </tr>

        <?php foreach ($alle_regels as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Naam']) ?></td>
                <td><?= htmlspecialchars($row['Serienummer']) ?></td>
                <td>
                    <?= $row['Uitgiftedatum'] ? date("d-m-Y", strtotime($row['Uitgiftedatum'])) : '-' ?>
                </td>
                <td>
                    <?= $row['Einddatum'] ? date("d-m-Y", strtotime($row['Einddatum'])) : '-' ?>
                </td>
                    <td><?= htmlspecialchars($row['Bedrijf']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</div>
</main>
</body>
</html>
