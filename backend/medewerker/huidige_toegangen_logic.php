<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/../config.php';

// Medewerkers ophalen
$medewerkers = $conn->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC")->fetch_all(MYSQLI_ASSOC);

// Kolommen ophalen
$columnsResult = $conn->query("SHOW COLUMNS FROM Medewerker");
$columns = $columnsResult->fetch_all(MYSQLI_ASSOC);

// Kolommen die GEEN toegang zijn
$exclude = ["Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf", "Referentie"];

// Medewerker ophalen
$medewerker = null;

if (isset($_GET['naam'])) {
    $naam = $_GET['naam'];

    $stmt = $conn->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
    $stmt->bind_param("s", $naam);
    $stmt->execute();
    $result = $stmt->get_result();
    $medewerker = $result->fetch_assoc();
}

/* ---------------------------------------------------
   1. Toevoegen / Verwijderen van toegang
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actie'])) {

    $veld = $_POST['veld'];
    $naam = $_POST['naam'];

    $waarde = ($_POST['actie'] === "toevoegen") ? 0 : 2;

    $stmt = $conn->prepare("UPDATE Medewerker SET `$veld` = ? WHERE Naam = ?");
    $stmt->bind_param("is", $waarde, $naam);
    $stmt->execute();

    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($naam));
    exit;
}

/* ---------------------------------------------------
   2. Opslaan van bovenste velden
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['functie']) && !isset($_POST['actie'])) {

    $stmt = $conn->prepare("
        UPDATE Medewerker 
        SET Functie=?, Locatie=?, Leidinggevende=?, Bedrijf=? 
        WHERE Naam=?
    ");
    $stmt->bind_param(
        "sssss",
        $_POST['functie'],
        $_POST['locatie'],
        $_POST['leidinggevende'],
        $_POST['bedrijf'],
        $_POST['naam']
    );
    $stmt->execute();

    $_SESSION['melding'] = "Gegevens succesvol opgeslagen!";

    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($_POST['naam']));
    exit;
}
