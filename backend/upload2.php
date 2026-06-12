<?php
require_once "config2.php"; // bevat $pdo

header("Content-Type: application/json");

// ---------------------------------------------------
// 1. JSON input ophalen
// ---------------------------------------------------
$rawInput = file_get_contents("php://input");
$input = json_decode($rawInput, true);

// Debug (optioneel)
// file_put_contents("debug_input.txt", $rawInput);

// ---------------------------------------------------
// 2. Validatie
// ---------------------------------------------------
if (!$input || !isset($input['email']) || !isset($input['fileData'])) {
    echo json_encode([
        "success" => false,
        "message" => "Vereiste velden ontbreken."
    ]);
    exit;
}

// ---------------------------------------------------
// 3. Variabelen ophalen
// ---------------------------------------------------
$email     = $input['email'];
$filename  = $input['filename'] ?? 'onbekend';
$mimetype  = $input['mimetype'] ?? 'application/octet-stream';

// Base64 opschonen (HEEL BELANGRIJK tegen corruptie)
$fileDataRaw = $input['fileData'];
$fileDataRaw = trim($fileDataRaw, '"');
$fileDataRaw = str_replace(["\r", "\n"], '', $fileDataRaw);

// Base64 decode
$fileData = base64_decode($fileDataRaw, true);

// ---------------------------------------------------
// 4. Controle decode
// ---------------------------------------------------
if ($fileData === false) {
    echo json_encode([
        "success" => false,
        "message" => "Base64 decode mislukt."
    ]);
    exit;
}

if (empty($fileData)) {
    echo json_encode([
        "success" => false,
        "message" => "Bestand is leeg."
    ]);
    exit;
}

// ---------------------------------------------------
// 5. Medewerker zoeken
// ---------------------------------------------------
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

// ---------------------------------------------------
// 6. Bestand opslaan in DB
// ---------------------------------------------------
$insert = $pdo->prepare("
    INSERT INTO MedewerkerBestanden 
    (MedewerkerEmail, BestandNaam, BestandType, BestandData)
    VALUES (?, ?, ?, ?)
");

// BELANGRIJK: bindParam voor BLOB
$insert->bindParam(1, $email);
$insert->bindParam(2, $filename);
$insert->bindParam(3, $mimetype, PDO::PARAM_STR);
$insert->bindParam(4, $fileData, PDO::PARAM_LOB);

$ok = $insert->execute();

if (!$ok) {
    echo json_encode([
        "success" => false,
        "message" => "Opslaan mislukt: " . implode(" | ", $insert->errorInfo())
    ]);
    exit;
}

// ---------------------------------------------------
// 7. Logboek
// ---------------------------------------------------
$log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
$log->execute([
    "$email heeft een bestand geupload via Power Automate",
    "Email"
]);

// ---------------------------------------------------
// 8. Succes response
// ---------------------------------------------------
echo json_encode([
    "success" => true,
    "message" => "Bestand succesvol opgeslagen in de database.",
    "email" => $email,
    "filename" => $filename
]);