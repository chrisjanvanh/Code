<?php
require_once "config2.php";

if (!isset($_POST['email']) || !isset($_POST['fileData'])) {
    echo json_encode([
        "success" => false,
        "message" => "Vereiste velden ontbreken."
    ]);
    exit;
}

$email     = $_POST['email'];
$filename  = $_POST['filename'] ?? 'onbekend';
$mimetype  = $_POST['mimetype'] ?? 'application/octet-stream';
$fileData  = base64_decode($_POST['fileData']);

/* ---------------------------------------------------
   1. Medewerker zoeken
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT Naam FROM Medewerker WHERE Email = ?");
$stmt->execute([$email]);
$medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medewerker) {
    echo json_encode([
        "success" => false,
        "message" => "Geen medewerker gevonden met dit e-mailadres."
    ]);
    exit;
}

/* ---------------------------------------------------
   2. Bestand opslaan
--------------------------------------------------- */
$insert = $pdo->prepare("
    INSERT INTO MedewerkerBestanden (MedewerkerEmail, BestandNaam, BestandType, BestandData)
    VALUES (?, ?, ?, ?)
");

$ok = $insert->execute([
    $email,
    $filename,
    $mimetype,
    $fileData
]);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$email heeft een bestand geupload voor zichzelf",
        "Email"
    ]);

if (!$ok) {
    echo json_encode([
        "success" => false,
        "message" => "Opslaan mislukt: " . implode(" | ", $insert->errorInfo())
    ]);
    exit;
}

/* ---------------------------------------------------
   3. Succesmelding
--------------------------------------------------- */
echo json_encode([
    "success" => true,
    "message" => "Bestand succesvol opgeslagen in de database.",
    "email" => $email,
    "filename" => $filename
]);