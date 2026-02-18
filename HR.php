<?php
require 'backend/config.php';
require 'backend/afronden.php';

$afdeling = "HR"; // deze pagina is voor HR

// Haal alle producten op waarvoor HR verantwoordelijk is
$stmt = $conn->prepare("SELECT * FROM Product WHERE Afdeling = ?");
$stmt->bind_param("s", $afdeling);
$stmt->execute();
$producten = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/HR.css">
    <script src="javascript/auth.js"></script>
    <script src="javascript/HR.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
<header>
    <img src="img/menu.png" alt="Menu button" class="menu-button" set onclick="toggleMenu()">
    <img src="img/logo.svg" alt="VDL Groep Logo">
    <a href="index.php">Homepagina</a>
    <a href="HuidigeToegangen.php">Medewerkers</a>
    <a href="HardwareToewijzen.php"> Hardware</a>
    <a href="Verantwoordelijke.php">Producten</a>
    <a href="HR.php" class="current">HR</a>
</header>

<div class="content">
    <h1>Taken</h1>

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
            // kolomnaam in Medewerker-tabel
            $kolom = $product['Product'];

            // Haal medewerkers op die 0 of 2 hebben
            $sql = "SELECT * FROM Medewerker WHERE `$kolom` IN (0,2)";
            $medewerkers = $conn->query($sql);
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
                            <button class="button" onclick="afronden(<?= json_encode($m['Naam']) ?>, <?= json_encode($kolom) ?>, 1)">Afronden</button>
                        <?php elseif ($m[$kolom] == 2): ?>
                            Verwijderen
                            <button class="button" onclick="afronden(<?= json_encode($m['Naam']) ?>, <?= json_encode($kolom) ?>, 0)">Afronden</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>

        <?php endwhile; ?>
    </table>

    <h2>Mailadressen voor deze afdeling</h2>

    <?php
    $emails = $conn->query("SELECT * FROM AfdelingEmails WHERE Afdeling = '$afdeling'");
    ?>

    <table>
        <tr>
            <th>Email</th>
            <th>Actie</th>
        </tr>

        <?php while ($e = $emails->fetch_assoc()): ?>
            <tr>
                <td><?= $e['Email'] ?></td>
                <td><button onclick="verwijderEmail(<?= $e['ID'] ?>)" class="button">Verwijder</button></td>
            </tr>
        <?php endwhile; ?>

        <tr>
            <td colspan="2">
                <button onclick="voegEmailToe()" class="button">Email toevoegen</button>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
