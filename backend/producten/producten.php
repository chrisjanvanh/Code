<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$afdeling = "Business IT";

$gebruikerEmail = $_SESSION['email'];

$stmt = $conn->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->bind_param("ss", $afdeling, $gebruikerEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: forbidden.php");
    exit;
}

$action = $_POST['action'] ?? '';
$id      = intval($_POST['id'] ?? 0);
$product = $_POST['product'] ?? '';
$contact = $_POST['contact'] ?? '';
$afdeling = $_POST['afdeling'] ?? '';

$ok = true;

// --- ACTIE: PRODUCT TOEVOEGEN ---
if ($action === "add") {

    if ($product === '') {
        echo "FOUT";
        exit;
    }

    // 1. Product toevoegen
    $stmt = $conn->prepare("INSERT INTO Product (Product, Contactpersoon, Afdeling) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $product, $contact, $afdeling);
    if (!$stmt->execute()) $ok = false;
    $stmt->close();

    // 2. Kolom toevoegen aan Medewerker
    $stmt2 = $conn->prepare("ALTER TABLE `Medewerker` ADD `$product` INT(11) NULL DEFAULT NULL");
    if (!$stmt2->execute()) $ok = false;
    $stmt2->close();

    echo $ok ? "OK" : "FOUT";
    exit;
}



// --- ACTIE: PRODUCT UPDATEN ---
if ($action === "update") {

    if ($id === 0) {
        echo "FOUT";
        exit;
    }

    $stmt = $conn->prepare("UPDATE Product SET Contactpersoon = ?, Afdeling = ? WHERE ID = ?");
    $stmt->bind_param("ssi", $contact, $afdeling, $id);

    echo $stmt->execute() ? "OK" : "FOUT";
    $stmt->close();
    exit;
}



// --- ACTIE: PRODUCT VERWIJDEREN ---
if ($action === "delete") {

    if ($id === 0) {
        echo "FOUT";
        exit;
    }

    // Productnaam ophalen
    $stmt = $conn->prepare("SELECT Product FROM Product WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($productnaam);
    $stmt->fetch();
    $stmt->close();

    if (!$productnaam) {
        echo "FOUT";
        exit;
    }

    // 1. Product verwijderen uit Product-tabel
    $stmt = $conn->prepare("DELETE FROM Product WHERE ID = ?");
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) $ok = false;
    $stmt->close();

    // 2. Kolom verwijderen uit Medewerker
    $sql = "ALTER TABLE `Medewerker` DROP COLUMN `$productnaam`";
    if (!$conn->query($sql)) {
        $ok = false;
    }

    echo $ok ? "OK" : "FOUT";
    exit;
}



// --- ONBEKENDE ACTIE ---
echo "FOUT: onbekende actie";
exit;
