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
$bedrijf = $_POST['bedrijf'] ?? '';

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* ---------------------------------------------------
   PRODUCT TOEVOEGEN
--------------------------------------------------- */
if ($action === "add") {

    if ($product === '') {
        echo "FOUT";
        exit;
    }

    // 1. Product toevoegen
    $stmt = $pdo->prepare("
        INSERT INTO Product (Product, Contactpersoon, Afdeling, Bedrijf)
        VALUES (?, ?, ?, ?)
    ");
    if (!$stmt->execute([$product, $contact, $afdeling, $bedrijf])) {
        echo "FOUT";
        exit;
    }

    // 2. Voor alle medewerkers van dit bedrijf een BedrijfProduct-regel aanmaken
    $stmtM = $pdo->prepare("SELECT Naam FROM Medewerker WHERE Bedrijf = ?");
    $stmtM->execute([$bedrijf]);
    $medewerkers = $stmtM->fetchAll(PDO::FETCH_COLUMN);

    $stmtBP = $pdo->prepare("
        INSERT INTO BedrijfProduct (Bedrijf, Product, Medewerker, Waarde)
        VALUES (?, ?, ?, NULL)
    ");

    foreach ($medewerkers as $m) {
        $stmtBP->execute([$bedrijf, $product, $m]);
    }

    // 3. Contactpersoon automatisch toevoegen aan AfdelingEmails (indien nog niet aanwezig)
    if (!empty($contact)) {

        // Bestaat deze combinatie al?
        $check = $pdo->prepare("
            SELECT 1 FROM AfdelingEmails 
            WHERE Email = ? AND Afdeling = ? AND Bedrijf = ?
        ");
        $check->execute([$contact, $afdeling, $bedrijf]);

        if ($check->rowCount() === 0) {
            $insert = $pdo->prepare("
                INSERT INTO AfdelingEmails (Afdeling, Email, Bedrijf)
                VALUES (?, ?, ?)
            ");
            $insert->execute([$afdeling, $contact, $bedrijf]);
        }
    }

    // 4. Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft het product $product toegevoegd aan bedrijf $bedrijf",
        "Producten"
    ]);

    echo "OK";
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

    // Productnaam + afdeling ophalen
    $stmt = $pdo->prepare("SELECT Product, Afdeling FROM Product WHERE ID = ? AND Bedrijf = ?");
    $stmt->execute([$id, $bedrijf]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "FOUT";
        exit;
    }

    $productnaam = $row['Product'];
    $productAfdeling = $row['Afdeling'];

    // 1. Product verwijderen uit Product-tabel
    $stmt = $pdo->prepare("DELETE FROM Product WHERE ID = ?");
    if (!$stmt->execute([$id])) $ok = false;

    // 2. Verwijderen uit BedrijfProduct
    $stmt = $pdo->prepare("DELETE FROM BedrijfProduct WHERE Product = ? AND Bedrijf = ?");
    $stmt->execute([$productnaam, $bedrijf]);

    // 3. Verwijder de combinatie Afdeling + Bedrijf uit AfdelingEmails
    $stmt = $pdo->prepare("
        DELETE FROM AfdelingEmails
        WHERE Afdeling = ? AND Bedrijf = ?
    ");
    $stmt->execute([$productAfdeling, $bedrijf]);

    // 4. Logboek
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
