<?php
session_start();
require 'backend/config.php';

// 1. Check of afdeling is meegegeven
if (!isset($_GET['afdeling'])) {
    header("Location: forbidden.php");
    exit;
}

$afdeling = $_GET['afdeling'];

// 2. Check of gebruiker is ingelogd (sessie moet email bevatten)
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$gebruikerEmail = $_SESSION['email'];

// 3. Controleer of gebruiker toegang heeft tot deze afdeling
$stmt = $conn->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->bind_param("ss", $afdeling, $gebruikerEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Geen toegang
    header("Location: forbidden.php");
    exit;
}

// 4. Haal producten op voor deze afdeling
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
    <link rel="stylesheet" href="css/Afdeling.css">
    <script src="javascript/auth.js"></script>
    <script src="javascript/Afdeling.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
<header>
    <img src="img/menu.png" alt="Menu button" class="menu-button" onclick="toggleMenu()">
    <img src="img/logo.svg" alt="VDL Groep Logo">
    <a href="index.php">Homepagina</a>
    <a href="HuidigeToegangen.php">Medewerkers</a>
    <a href="HardwareToewijzen.php"> Hardware</a>
    <a href="Verantwoordelijke.php">Producten</a>
    <a href="Afdeling.php?afdeling=HR" class="current">HR</a>
</header>

<script>const afdeling = "<?= $afdeling ?>";</script>

<div class="content">
    <h1>Taken voor <?= $afdeling ?></h1>

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
                            <button class="button" onclick='afronden(<?= json_encode($m["Naam"]) ?>, <?= json_encode($kolom) ?>, 1)'>Afronden</button>
                        <?php elseif ($m[$kolom] == 2): ?>
                            Verwijderen
                            <button class="button" onclick='afronden(<?= json_encode($m["Naam"]) ?>, <?= json_encode($kolom) ?>, 0)'>Afronden</button>
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
                <td><button onclick="verwijderEmail(<?= $e['ID'] ?>)" class="button" style="background-color: #ff4d4d;">Verwijder</button></td>
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
