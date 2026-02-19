<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

$medewerkers = $conn->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC")->fetch_all(MYSQLI_ASSOC);
$hardware = $conn->query("SELECT Serienummer FROM Hardware ORDER BY Serienummer ASC")->fetch_all(MYSQLI_ASSOC);

$melding = "";

/* ---------------------------------------------------
   OPSLAAN VAN NIEUWE TOEWĲZING
--------------------------------------------------- */
if (isset($_POST['opslaan'])) {

    $naam = trim($_POST['naam']);
    $serienummer = trim($_POST['serienummer']);
    $uitgiftedatum = $_POST['uitgiftedatum'];

    if ($naam === "" || $serienummer === "" || $uitgiftedatum === "") {
        $melding = "Vul alle velden in.";
    } else {

        // Check medewerker
        $checkNaam = $conn->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
        $checkNaam->bind_param("s", $naam);
        $checkNaam->execute();
        $checkNaam->store_result();

        if ($checkNaam->num_rows == 0) {
            $melding = "Deze medewerker bestaat niet.";
        } else {

            // Check hardware
            $checkHW = $conn->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
            $checkHW->bind_param("s", $serienummer);
            $checkHW->execute();
            $checkHW->store_result();

            if ($checkHW->num_rows == 0) {
                $melding = "Dit serienummer bestaat niet in de hardwarelijst.";
            } else {

                // Check of al toegewezen
                $checkSN = $conn->prepare("SELECT 1 FROM Gebruikname WHERE Serienummer = ?");
                $checkSN->bind_param("s", $serienummer);
                $checkSN->execute();
                $checkSN->store_result();

                if ($checkSN->num_rows > 0) {
                    $melding = "Dit serienummer is al toegewezen.";
                } else {

                    // INSERT
                    $stmt = $conn->prepare("
                        INSERT INTO Gebruikname (Serienummer, Naam, Uitgiftedatum)
                        VALUES (?, ?, ?)
                    ");
                    $stmt->bind_param("sss", $serienummer, $naam, $uitgiftedatum);

                    if ($stmt->execute()) {
                        $_SESSION['melding'] = "Hardware succesvol toegewezen!";
                        header("Location: ../HardwareToewijzen.php");
                        exit;
                    } else {
                        error_log("Insert error: " . $stmt->error, 3, __DIR__ . "/error.log");
                        $melding = "Er is iets fout gegaan, probeer het later opnieuw.";
                    }
                }
            }
        }
    }
}

/* ---------------------------------------------------
   VERWIJDEREN
--------------------------------------------------- */
if (isset($_POST['verwijder'])) {

    $sn = $_POST['verwijder'];
    $type = $_POST['type'];

    if ($type === "toegewezen") {
        // Verwijder uit Gebruikname
        $del = $conn->prepare("DELETE FROM Gebruikname WHERE Serienummer = ?");
        $del->bind_param("s", $sn);
        $del->execute();
    }

    if ($type === "voorraad") {
        // Verwijder uit Hardware
        $del = $conn->prepare("DELETE FROM Hardware WHERE Serienummer = ?");
        $del->bind_param("s", $sn);
        $del->execute();
    }

    header("Location: ../HardwareToewijzen.php");
    exit;
}

// Niet-toegewezen hardware ophalen
$voorraad = $conn->query("
    SELECT Serienummer, 'Voorraad' AS Naam, NULL AS Uitgiftedatum, 'voorraad' AS type
    FROM Hardware
    WHERE Serienummer NOT IN (SELECT Serienummer FROM Gebruikname)
")->fetch_all(MYSQLI_ASSOC);

// Toegewezen hardware ophalen
$toegewezen = $conn->query("
    SELECT Serienummer, Naam, Uitgiftedatum, 'toegewezen' AS type
    FROM Gebruikname
    ORDER BY Uitgiftedatum DESC
")->fetch_all(MYSQLI_ASSOC);

// Voorraad bovenaan
$alle_regels = array_merge($voorraad, $toegewezen);
