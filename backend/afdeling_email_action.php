<?php
require 'config.php';

if (!isset($_POST['action'])) {
    echo "FOUT: geen actie opgegeven";
    exit;
}

$action = $_POST['action'];

if ($action === "delete") {

    if (!isset($_POST['id'])) {
        echo "FOUT: geen ID ontvangen";
        exit;
    }

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM AfdelingEmails WHERE ID = ?");
    $stmt->bind_param("i", $id);
    echo $stmt->execute() ? "OK" : "FOUT: " . $stmt->error;
    exit;
}

if ($action === "add") {

    if (!isset($_POST['email'], $_POST['afdeling'])) {
        echo "FOUT: ontbrekende parameters";
        exit;
    }

    $email = $_POST['email'];
    $afdeling = $_POST['afdeling'];

    $stmt = $conn->prepare("INSERT INTO AfdelingEmails (Afdeling, Email) VALUES (?, ?)");
    $stmt->bind_param("ss", $afdeling, $email);
    echo $stmt->execute() ? "OK" : "FOUT: " . $stmt->error;
    exit;
}

echo "FOUT: onbekende actie";
