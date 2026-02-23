<?php
require_once "config.php";

if (!isset($_POST['email']) || !isset($_FILES['file'])) {
    echo json_encode([
        "success" => false,
        "message" => "Vereiste velden ontbreken."
    ]);
    exit;
}

$email = $_POST['email'];
$filename = $_POST['filename'] ?? $_FILES['file']['name'];
$mimetype = $_POST['mimetype'] ?? $_FILES['file']['type'];

// Lees bestand in
$fileData = file_get_contents($_FILES['file']['tmp_name']);

// Zoek medewerker
$stmt = $conn->prepare("SELECT Naam FROM Medewerker WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Geen medewerker gevonden met dit e-mailadres."
    ]);
    exit;
}

$insert = $conn->prepare("
    INSERT INTO MedewerkerBestanden (MedewerkerEmail, BestandNaam, BestandType, BestandData)
    VALUES (?, ?, ?, ?)
");

$insert->bind_param("ssss", $email, $filename, $mimetype, $fileData);
$insert->execute();

echo json_encode([
    "success" => true,
    "message" => "Bestand succesvol opgeslagen in de database.",
    "email" => $email,
    "filename" => $filename
]);
?>
