<?php
require_once "config2.php"; // bevat $pdo

if (!isset($_POST['email']) || !isset($_FILES['file'])) {
    echo json_encode([
        "success" => false,
        "message" => "Vereiste velden ontbreken."
    ]);
    exit;
}

$email     = $_POST['email'];
$filename  = $_POST['filename'] ?? $_FILES['file']['name'];
$mimetype  = $_POST['mimetype'] ?? $_FILES['file']['type'];
$fileData  = file_get_contents($_FILES['file']['tmp_name']);

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
