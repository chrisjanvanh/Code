<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Bedrijven ophalen waar gebruiker toegang toe heeft
$stmt = $pdo->prepare("
    SELECT DISTINCT Bedrijf 
    FROM AfdelingEmails 
    WHERE Email = ? AND Afdeling = 'Business IT'
");
$stmt->execute([$gebruikerEmail]);
$bedrijvenUser = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!in_array($bedrijf, $bedrijvenUser)) {
    echo "FOUT: geen toegang tot dit bedrijf";
    exit;
}

$action   = $_POST['action'] ?? '';
$id       = intval($_POST['id'] ?? 0);
$product  = $_POST['product'] ?? '';
$contact  = $_POST['contact'] ?? '';
$afdeling = $_POST['afdeling'] ?? '';
$bedrijf = $_POST['bedrijf'] ?? '';

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
    $stmt = $pdo->prepare("INSERT INTO Product (Product, Contactpersoon, Afdeling, bedrijf) VALUES (?, ?, ?, ?)");
    if (!$stmt->execute([$product, $contact, $afdeling, $bedrijf])) $ok = false;

    $afdelingstmt = $pdo->prepare("INSERT INTO AfdelingEmails (Afdeling, Email, Bedrijf) VALUES (?, ?, ?)");
    if (!$afdelingstmt->execute([$afdeling, $contact, $bedrijf])) $ok = false;

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
