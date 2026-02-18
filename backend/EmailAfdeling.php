<?php
//Verwijderen
require 'backend/config.php';

if (!isset($_POST['id'])) {
    echo "FOUT: geen ID ontvangen";
    exit;
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("DELETE FROM AfdelingEmails WHERE ID = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "FOUT: " . $stmt->error;
}

$stmt->close();



// Toevoegen
<?php
require 'backend/config.php';

if (!isset($_POST['email'], $_POST['afdeling'])) {
    echo "FOUT: ontbrekende parameters";
    exit;
}

$email = $_POST['email'];
$afdeling = $_POST['afdeling'];

$stmt = $conn->prepare("INSERT INTO AfdelingEmails (Afdeling, Email) VALUES (?, ?)");
$stmt->bind_param("ss", $afdeling, $email);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "FOUT: " . $stmt->error;
}

$stmt->close();
