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

require_once "config2.php";

if (!isset($_GET['id'])) {
    die("Geen ID opgegeven.");
}

$id = $_GET['id'];

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

$stmt = $pdo->prepare("
    SELECT MedewerkerEmail, BestandNaam, BestandType, BestandData
    FROM MedewerkerBestanden
    WHERE ID = ?
");
$stmt->execute([$id]);
$bestand = $stmt->fetch(PDO::FETCH_ASSOC);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        $gebruikerNaam . " heeft het bestand van " . $bestand['MedewerkerEmail'] . " genaamd " . $bestand['BestandNaam'] . " gedownload",
        "Huidige Medewerker"
    ]);

    $melding = "Bestand succesvol gedownload"

if (!$bestand) {
    die("Geen bestand gevonden.");
}

$naam = $bestand['BestandNaam'];
$type = $bestand['BestandType'];
$data = $bestand['BestandData'];

header("Content-Type: $type");
header("Content-Disposition: attachment; filename=\"$naam\"");
header("Content-Length: " . strlen($data));

echo $data;
exit;
