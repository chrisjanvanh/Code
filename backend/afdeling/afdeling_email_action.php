<?php
session_start();
require __DIR__ . '/../config2.php';

if (!isset($_POST['action'])) {
    echo "FOUT: geen actie opgegeven";
    exit;
}

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";
$action = $_POST['action'];

/* ---------------- DELETE ---------------- */
if ($action === "delete") {

    if (!isset($_POST['id'])) {
        echo "FOUT: geen ID ontvangen";
        exit;
    }

    $id = intval($_POST['id']);

    // Email + afdeling ophalen voor logboek
    $stmtInfo = $pdo->prepare("SELECT Email, Afdeling FROM AfdelingEmails WHERE ID = ?");
    $stmtInfo->execute([$id]);
    $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);

    if (!$info) {
        echo "FOUT: record niet gevonden";
        exit;
    }

    $email = $info['Email'];
    $afdeling = $info['Afdeling'];

    try {
        // Verwijderen
        $stmt = $pdo->prepare("DELETE FROM AfdelingEmails WHERE ID = ?");
        $stmt->execute([$id]);

        // Logboek
        $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
        $log->execute([
            "Email $email verwijderd uit afdeling $afdeling door $gebruikerNaam",
            "Afdelingen"
        ]);

        echo "OK";
    } catch (PDOException $e) {
        echo "FOUT: " . $e->getMessage();
    }

    exit;
}

/* ---------------- ADD ---------------- */
if ($action === "add") {

    if (!isset($_POST['email'], $_POST['afdeling'])) {
        echo "FOUT: ontbrekende parameters";
        exit;
    }

    $email = $_POST['email'];
    $afdeling = $_POST['afdeling'];

    try {
        // Toevoegen
        $stmt = $pdo->prepare("INSERT INTO AfdelingEmails (Afdeling, Email) VALUES (?, ?)");
        $stmt->execute([$afdeling, $email]);

        // Logboek
        $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
        $log->execute([
            "Email $email toegevoegd aan afdeling $afdeling door $gebruikerNaam",
            "Afdelingen"
        ]);

        echo "OK";
    } catch (PDOException $e) {
        echo "FOUT: " . $e->getMessage();
    }

    exit;
}

echo "FOUT: onbekende actie";
