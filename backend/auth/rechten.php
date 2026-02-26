<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

// Rechten ophalen
$rechten = [];

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT Afdeling FROM AfdelingEmails WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $rechten[] = $row['Afdeling'];
    }
}

// Functie om menu te tonen
function toonMenu($rechten, $current = "")
{
    ?>
    <header>
        <img src="img/menu.png" alt="Menu button" class="menu-button" onclick="toggleMenu()">
        <a href="index.php" class="logo-btn"><img src="img/logo.svg" alt="VDL Groep Logo"></a>

        <a href="index.php" class="<?= $current === 'index' ? 'current' : '' ?>">Homepagina</a>

        <?php if (in_array("HR", $rechten)): ?>
            <a href="HuidigeToegangen.php" class="<?= $current === 'medewerkers' ? 'current' : '' ?>">Medewerkers</a>
        <?php endif; ?>

        <?php if (in_array("Business IT", $rechten)): ?>
            <a href="HardwareToewijzen.php" class="<?= $current === 'hardware' ? 'current' : '' ?>">Hardware</a>
        <?php endif; ?>

        <?php if (in_array("Business IT", $rechten)): ?>
        <a href="Verantwoordelijke.php" class="<?= $current === 'producten' ? 'current' : '' ?>">Producten</a>
        <?php endif; ?>

        <?php if (!empty($rechten)): ?>
            <a href="afdelingRouter.php" class="<?= $current === 'taken' ? 'current' : '' ?>">Taken</a>
        <?php endif; ?>

        <div style="position: absolute; right: 15px; display: flex; align-items: center;">
            <a href="logout.php"
               style="padding: 8px 15px; background: #e74c3c; color: white;
                      border-radius: 5px; text-decoration: none;">
                Uitloggen
            </a>
        </div>
    </header>
    <?php
}
