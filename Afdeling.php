<?php require 'backend/afdeling/afdeling_logic.php'; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/Afdeling.css">

    <script src="javascript/main.js" defer></script>
    <script src="javascript/Afdeling.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<header>
    <img src="img/menu.png" alt="Menu button" class="menu-button" onclick="toggleMenu()">
    <img src="img/logo.svg" alt="VDL Groep Logo">

    <a href="index.php">Homepagina</a>
    <a href="HuidigeToegangen.php">Medewerkers</a>
    <a href="HardwareToewijzen.php">Hardware</a>
    <a href="Verantwoordelijke.php">Producten</a>
    <a href="afdelingRouter.php" class="current">Taken</a>

    <div class="logout-container">
        <a href="logout.php" class="logout-button">Uitloggen</a>
    </div>
</header>

<script>
    const afdeling = "<?= htmlspecialchars($afdeling) ?>";
</script>

<div class="content">
    <h1>Taken voor <?= htmlspecialchars($afdeling) ?></h1>

    <table>
        <tr>
            <th>Product</th>
            <th>Medewerker</th>
            <th>Functie</th>
            <th>Locatie</th>
            <th>Leidinggevende</th>
            <th>Bedrijf</th>
            <th>Referentie</th>
            <th>Taak</th>
        </tr>

        <?php while ($product = $producten->fetch_assoc()): ?>
            <?php
                $kolom = $product['Product'];
                $medewerkers = $conn->query("SELECT * FROM Medewerker WHERE `$kolom` IN (0,2)");
            ?>

            <?php while ($m = $medewerkers->fetch_assoc()): ?>
                <tr>
                    <td><?= $product['Product'] ?></td>
                    <td><?= $m['Naam'] ?></td>
                    <td><?= $m['Functie'] ?></td>
                    <td><?= $m['Locatie'] ?></td>
                    <td><?= $m['Leidinggevende'] ?></td>
                    <td><?= $m['Bedrijf'] ?></td>
                    <td><?= $m['Referentie'] ?></td>

                    <td>
                        <?php if ($m[$kolom] == 0): ?>
                            Toevoegen
                            <button class="button" onclick='afronden("<?= $m["Naam"] ?>", "<?= $kolom ?>", 1)'>Afronden</button>
                        <?php else: ?>
                            Verwijderen
                            <button class="button" onclick='afronden("<?= $m["Naam"] ?>", "<?= $kolom ?>", 0)'>Afronden</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endwhile; ?>
    </table>

    <h2>Mailadressen voor deze afdeling</h2>

    <table>
        <tr>
            <th>Email</th>
            <th>Actie</th>
        </tr>

        <?php while ($e = $emails->fetch_assoc()): ?>
            <tr>
                <td><?= $e['Email'] ?></td>
                <td>
                    <button class="button delete" onclick="verwijderEmail(<?= $e['ID'] ?>)">Verwijderen</button>
                </td>
            </tr>
        <?php endwhile; ?>

        <tr>
            <td colspan="2">
                <button class="button" onclick="voegEmailToe()">Email toevoegen</button>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
