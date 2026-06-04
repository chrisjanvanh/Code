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

if (!isset($_SESSION['email'])) {
    header("Location: /../login.php");
    exit;
}

require "config2.php";

$bedrijf = $_GET['bedrijf'] ?? '';
if ($bedrijf === '') {
    header("Location: ../HardwareToewijzen.php");
    exit;
}

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=hardware{$bedrijf}_" . date("Y-m-d_H-i-s") . ".csv");
echo "\xEF\xBB\xBF";

$output = fopen("php://output", "w");
fputcsv($output, ["Naam", "Serienummer", "Model", "Uitgiftedatum"], ";", '"', "\\");

// Voorraad
$stmt = $pdo->prepare("
    SELECT Serienummer, Model, 'Voorraad' AS Naam, NULL AS Uitgiftedatum
    FROM Hardware
    WHERE Bedrijf = ?
    AND Serienummer NOT IN (SELECT Serienummer FROM Gebruikname)
");
$stmt->execute([$bedrijf]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        "Voorraad",
        $row['Serienummer'],
        $row['Model'],
        ''
    ], ";", '"', "\\");
}

// Toegewezen
$stmt = $pdo->prepare("
    SELECT g.Serienummer, h.Model, m.Naam, g.Uitgiftedatum
    FROM Gebruikname g
    JOIN Hardware h ON g.Serienummer = h.Serienummer
    JOIN Medewerker m ON g.MedewerkerID = m.MedewerkerID
    WHERE h.Bedrijf = ?
    ORDER BY m.Naam ASC
");
$stmt->execute([$bedrijf]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['Naam'],
        $row['Serienummer'],
        $row['Model'],
        '="' . date("d-m-Y", strtotime($row['Uitgiftedatum'])) . '"'
    ], ";", '"', "\\");
}

fclose($output);
exit;