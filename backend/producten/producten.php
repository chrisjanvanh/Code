<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$action   = $_POST['action'] ?? '';
$id       = intval($_POST['id'] ?? 0);
$product  = $_POST['product'] ?? '';
$contact  = $_POST['contact'] ?? '';
$afdeling = $_POST['afdeling'] ?? '';

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

$ok = true;

/* ---------------------------------------------------
   PRODUCT TOEVOEGEN
--------------------------------------------------- */
if ($action === "add") {

    if ($product === '') {
        echo "FOUT";
        exit;
    }

    // 1. Product toevoegen
    $stmt = $pdo->prepare("INSERT INTO Product (Product, Contactpersoon, Afdeling) VALUES (?, ?, ?)");
    if (!$stmt->execute([$product, $contact, $afdeling])) $ok = false;

    // 2. Kolom toevoegen aan Medewerker
    try {
        $pdo->exec("ALTER TABLE `Medewerker` ADD `$product` INT(11) NULL DEFAULT NULL");
    } catch (PDOException $e) {
        $ok = false;
    }

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft het product $product toegevoegd aan producten",
        "Producten"
    ]);

    echo $ok ? "OK" : "FOUT";
    exit;
}

/* ---------------------------------------------------
   PRODUCT UPDATEN
--------------------------------------------------- */
if ($action === "update") {

    if ($id === 0) {
        echo "FOUT";
        exit;
    }

    $stmt = $pdo->prepare("UPDATE Product SET Contactpersoon = ?, Afdeling = ? WHERE ID = ?");
    echo $stmt->execute([$contact, $afdeling, $id]) ? "OK" : "FOUT";

    $stmt2 = $pdo->prepare("SELECT Product FROM Product WHERE ID = ?");
    $stmt2->execute([$id]);
    $productnaam = $stmt2->fetchColumn();

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft het product $productnaam gewijzigd naar $contact en $afdeling",
        "Producten"
    ]);

    exit;
}

/* ---------------------------------------------------
   PRODUCT VERWIJDEREN
--------------------------------------------------- */
if ($action === "delete") {

    if ($id === 0) {
        echo "FOUT";
        exit;
    }

    // Productnaam ophalen
    $stmt = $pdo->prepare("SELECT Product FROM Product WHERE ID = ?");
    $stmt->execute([$id]);
    $productnaam = $stmt->fetchColumn();

    if (!$productnaam) {
        echo "FOUT";
        exit;
    }

    // 1. Product verwijderen uit Product-tabel
    $stmt = $pdo->prepare("DELETE FROM Product WHERE ID = ?");
    if (!$stmt->execute([$id])) $ok = false;

    // 2. Kolom verwijderen uit Medewerker
    try {
        $pdo->exec("ALTER TABLE `Medewerker` DROP COLUMN `$productnaam`");
    } catch (PDOException $e) {
        $ok = false;
    }

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft het product $productnaam verwijderd",
        "Producten"
    ]);

    echo $ok ? "OK" : "FOUT";
    exit;
}

/* ---------------------------------------------------
   ONBEKENDE ACTIE
--------------------------------------------------- */
echo "FOUT: onbekende actie";
exit;
