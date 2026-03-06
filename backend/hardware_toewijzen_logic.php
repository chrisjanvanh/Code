<?php
session_start();
require 'config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$afdeling = "Business IT";
$gebruikerEmail = $_SESSION['email'];

/* ---------------------------------------------------
   1. Controle: heeft gebruiker toegang?
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdeling, $gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: forbidden.php");
    exit;
}

/* ---------------------------------------------------
   2. Medewerkers + hardware ophalen
--------------------------------------------------- */
$medewerkers = $pdo->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC")
                   ->fetchAll(PDO::FETCH_ASSOC);

$hardware = $pdo->query("SELECT Serienummer, 'Voorraad' AS Naam, NULL AS Uitgiftedatum, 'voorraad' AS type
    FROM Hardware
    WHERE Serienummer NOT IN (SELECT Serienummer FROM Gebruikname)")
                ->fetchAll(PDO::FETCH_ASSOC);

$melding = "";

/* ---------------------------------------------------
   3. OPSLAAN VAN NIEUWE TOEWĲZING
--------------------------------------------------- */
if (isset($_POST['opslaan'])) {

    $naam         = trim($_POST['naam']);
    $serienummer  = trim($_POST['serienummer']);
    $uitgiftedatum = $_POST['uitgiftedatum'];

    if ($naam === "" || $serienummer === "" || $uitgiftedatum === "") {
        $melding = "Vul alle velden in.";
    } else {

        // Check medewerker
        $checkNaam = $pdo->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
        $checkNaam->execute([$naam]);

        if ($checkNaam->rowCount() === 0) {
            $melding = "Deze medewerker bestaat niet.";
        } else {

            // Check hardware
            $checkHW = $pdo->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
            $checkHW->execute([$serienummer]);

            if ($checkHW->rowCount() === 0) {
                $melding = "Dit serienummer bestaat niet in de hardwarelijst.";
            } else {

                // Check of al toegewezen
                $checkSN = $pdo->prepare("SELECT 1 FROM Gebruikname WHERE Serienummer = ?");
                $checkSN->execute([$serienummer]);

                if ($checkSN->rowCount() > 0) {
                    $melding = "Dit serienummer is al toegewezen.";
                } else {

                    // INSERT
                    $stmt = $pdo->prepare("
                        INSERT INTO Gebruikname (Serienummer, Naam, Uitgiftedatum)
                        VALUES (?, ?, ?)
                    ");

                    $ok = $stmt->execute([$serienummer, $naam, $uitgiftedatum]);

                    if ($ok) {
                        $_SESSION['melding'] = "Hardware succesvol toegewezen!";
                        header("Location: ../HardwareToewijzen.php");
                        exit;
                    } else {
                        error_log("Insert error: " . implode(" | ", $stmt->errorInfo()), 3, __DIR__ . "/error.log");
                        $melding = "Er is iets fout gegaan, probeer het later opnieuw.";
                    }
                }
            }
        }
    }
}

/* ---------------------------------------------------
   4. VERWIJDEREN
--------------------------------------------------- */
if (isset($_POST['verwijder'])) {

    $sn   = $_POST['verwijder'];
    $type = $_POST['type'];

    if ($type === "toegewezen") {
        $del = $pdo->prepare("DELETE FROM Gebruikname WHERE Serienummer = ?");
        $del->execute([$sn]);
    }

    if ($type === "voorraad") {
        $del = $pdo->prepare("DELETE FROM Hardware WHERE Serienummer = ?");
        $del->execute([$sn]);
    }

    header("Location: ../HardwareToewijzen.php");
    exit;
}

/* ---------------------------------------------------
   5. Niet-toegewezen hardware ophalen
--------------------------------------------------- */
$voorraad = $pdo->query("
    SELECT Serienummer, 'Voorraad' AS Naam, NULL AS Uitgiftedatum, 'voorraad' AS type
    FROM Hardware
    WHERE Serienummer NOT IN (SELECT Serienummer FROM Gebruikname)
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------
   6. Toegewezen hardware ophalen
--------------------------------------------------- */
$toegewezen = $pdo->query("
    SELECT Serienummer, Naam, Uitgiftedatum, 'toegewezen' AS type
    FROM Gebruikname
    ORDER BY Uitgiftedatum DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------
   7. Voorraad bovenaan
--------------------------------------------------- */
$alle_regels = array_merge($voorraad, $toegewezen);
