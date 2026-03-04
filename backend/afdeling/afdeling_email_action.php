<?php
require __DIR__ . '/../config2.php';

if (!isset($_POST['action'])) {
    echo "FOUT: geen actie opgegeven";
    exit;
}

$_SESSION['gebruikernaam'] = $_POST['gebruikernaam'];
$gebruikerNaam  = $_SESSION['gebruikernaam'] ?? "Onbekend";

$action = $_POST['action'];

if ($action === "delete") {

    if (!isset($_POST['id'])) {
        echo "FOUT: geen ID ontvangen";
        exit;
    }

    $id = intval($_POST['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM AfdelingEmails WHERE ID = ?");
        $stmt->execute([$id]);

        $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
        $log->execute(["Email $email verwijderd aan afdeling $afdeling door $gebruikerNaam", "Afdelingen"]);

        echo "OK";
    } catch (PDOException $e) {
        echo "FOUT: " . $e->getMessage();
    }

    exit;
}

if ($action === "add") {

    if (!isset($_POST['email'], $_POST['afdeling'])) {
        echo "FOUT: ontbrekende parameters";
        exit;
    }

    $email = $_POST['email'];
    $afdeling = $_POST['afdeling'];

    try {
        $stmt = $pdo->prepare("INSERT INTO AfdelingEmails (Afdeling, Email) VALUES (?, ?)");
        $stmt->execute([$afdeling, $email]);

        $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
        $log->execute(["Email $email toegevoegd aan afdeling $afdeling door $gebruikerNaam", "Afdelingen"]);

        echo "OK";
    } catch (PDOException $e) {
        echo "FOUT: " . $e->getMessage();
    }

    exit;
}

echo "FOUT: onbekende actie";
