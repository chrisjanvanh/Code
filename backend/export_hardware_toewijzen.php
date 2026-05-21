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
header("Content-Disposition: attachment; filename=hardware_toewijzen_{$bedrijf}_" . " " . date("Ymd_His") . ".csv");
echo "\xEF\xBB\xBF";

$output = fopen("php://output", "w");
fputcsv($output, ["Naam", "Serienummer", "Model", "Uitgiftedatum"], ";", '"', "\\");

$stmt = $pdo->prepare("
    SELECT Serienummer, Model, 'Voorraad' AS Naam, NULL AS Uitgiftedatum, 'Voorraad' AS Type
    FROM Hardware
    WHERE Bedrijf = ?
    AND Serienummer NOT IN (SELECT Serienummer FROM Gebruikname)
");
$stmt->execute([$bedrijf]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        "Voorraad Desktop",
        $row['Serienummer'],
        $row['Model'],
        ''
    ], ";", '"', "\\");
}

$stmt = $pdo->prepare("
    SELECT g.Serienummer, h.Model, g.Naam, g.Uitgiftedatum, 'Toegewezen' AS Type
    FROM Gebruikname g
    JOIN Hardware h ON g.Serienummer = h.Serienummer
    WHERE h.Bedrijf = ?
    ORDER BY g.Naam ASC
");
$stmt->execute([$bedrijf]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['Naam'],
        $row['Serienummer'],
        $row['Model'],
        date("d-m-Y", strtotime($row['Uitgiftedatum']))
    ], ";", '"', "\\");

}

fclose($output);
exit;