<?php
header("Cross-Origin-Opener-Policy: unsafe-none");
header("Cross-Origin-Embedder-Policy: unsafe-none");

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/backend/medewerker/Openstaand_logic.php';

$statusMap = [
    0 => "Aangevraagd",
    2 => "Verwijdering aangevraagd",
    3 => "Wachtrij"
];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interne VDL Bus & Coach portal voor hardwarebeheer, bestellingen en medewerkerstoegang.">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'medewerkers');
?>

<main>
    <div class="content">
        
    <h2>Openstaande aanvragen</h2>

    <?php if (empty($openstaandeTaken)): ?>
        <p>Geen openstaande aanvragen gevonden.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>Taak</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($openstaandeTaken as $taak): ?>
                    <tr>
                        <td><?= htmlspecialchars($taak['Medewerker']) ?></td>
                        <td><?= htmlspecialchars($taak['Product']) ?></td>
                        <td>
                            <?php
                            $kleur = match ($taak['Waarde']) {
                                0 => "orange",
                                2 => "lightgray",
                                3 => "yellow",
                                default => "black"
                            };
                            ?>
                            <span style="color: <?= $kleur ?>">
                                <?= $statusMap[$taak['Waarde']] ?? 'Onbekend' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>
</main>

</body>
</html>