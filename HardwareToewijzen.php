<?php 
require 'backend/hardware_toewijzen_logic.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<div class="content">

    <?php if (!empty($_SESSION['melding'])): ?>
        <div class="melding"><?= $_SESSION['melding'] ?></div>
        <?php unset($_SESSION['melding']); ?>
    <?php endif; ?>

    <?php if (!empty($melding)): ?>
        <div class="melding"><?= $melding ?></div>
    <?php endif; ?>

    <h1>Hardware Toewijzen</h1>

    <form method="POST">
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

        Uitgiftedatum:<br>
        <input type="date" id="uitgiftedatum" name="uitgiftedatum">

        <button class="button" name="opslaan">Opslaan</button>
        <button type="button" class="button" onclick="naamzoeken()">Zoeken</button>
        <a href="HardwareToevoegen.php" class="button">Hardware Toevoegen</a>
        <a href="GeschiedenisHardware.php" class="button">Geschiedenis</a>
    </form>

    <br><br>
    <h3>Toegewezen Hardware</h3>
    <table id="Toegewezen">
        <tr>
            <th>Naam</th>
            <th>Serienummer</th>
            <th>Uitgiftedatum</th>
            <th>Verwijderen</th>
        </tr>

        <?php foreach ($alle_regels as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Naam']) ?></td>
                <td><?= htmlspecialchars($row['Serienummer']) ?></td>
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

</div>
</body>
</html>
