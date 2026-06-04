<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

session_start();

}

require_once __DIR__ . '/../config2.php';

// Rechten ophalen
$rechten = [];

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $stmt = $pdo->prepare("SELECT Afdeling FROM AfdelingEmails WHERE Email = ?");
    $stmt->execute([$email]);

    // Haal alleen de kolom 'Afdeling' op als simpele array
    $rechten = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Functie om menu te tonen
function toonMenu($rechten, $current = "")
{
    ?>
    <header>
        <img src="img/menu.png" alt="Menu button" class="menu-button" onclick="toggleMenu()">
        <a href="index.php" class="logo-btn"><img src="img/logo.svg" alt="VDL Groep Logo"></a>

        <a href="index.php" class="<?= $current === 'index' ? 'current' : '' ?>">Homepagina</a>

        <?php if (!empty($rechten)): ?>
            <a href="HuidigeToegangen.php" class="<?= $current === 'medewerkers' ? 'current' : '' ?>">Medewerkers</a>
        <?php endif; ?>

        <?php if (in_array("IT Contact", $rechten)): ?>
            <a href="HardwareToewijzen.php" class="<?= $current === 'hardware' ? 'current' : '' ?>">Hardware</a>
        <?php endif; ?>

        <?php if (in_array("IT Contact", $rechten)): ?>
            <a href="Verantwoordelijke.php" class="<?= $current === 'producten' ? 'current' : '' ?>">Producten</a>
        <?php endif; ?>

        <?php if (!empty($rechten)): ?>
            <a href="afdelingRouter.php" class="<?= $current === 'taken' ? 'current' : '' ?>">Taken</a>
        <?php endif; ?>

        <?php if (in_array("IT Contact", $rechten)): ?>
            <a href="Logboek.php" class="<?= $current === 'logboek' ? 'current' : '' ?>">Logboek</a>
        <?php endif; ?>

        <div class="logout-container"> 
            <a href="logout.php" class="logout-button">
                Uitloggen
            </a>
        </div>
    </header>
    <?php
}
