<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$gebruikerEmail = $_SESSION['email'];

/* ---------------------------------------------------
   1. Haal alle afdelingen + bedrijven op
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT Afdeling, Bedrijf FROM AfdelingEmails WHERE Email = ?");
$stmt->execute([$gebruikerEmail]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------
   2. Geen afdelingen → forbidden
--------------------------------------------------- */
if (empty($rows)) {
    header("Location: forbidden.php");
    exit;
}

/* ---------------------------------------------------
   3. Eén afdeling → direct doorsturen
--------------------------------------------------- */
if (count($rows) === 1) {
    $afd = urlencode($rows[0]['Afdeling']);
    $bedrijf = urlencode($rows[0]['Bedrijf']);
    header("Location: Afdeling.php?afdeling=$afd&bedrijf=$bedrijf");
    exit;
}

/* ---------------------------------------------------
   4. Meerdere afdelingen → lijst tonen
--------------------------------------------------- */
$afdelingen = $rows;

$bedrijven_gegroepeerd = [];

foreach ($afdelingen as $afd) {
    $bedrijf = $afd['Bedrijf'];
    if (!isset($bedrijven_gegroepeerd[$bedrijf])) {
        $bedrijven_gegroepeerd[$bedrijf] = [];
    }
    $bedrijven_gegroepeerd[$bedrijf][] = $afd['Afdeling'];
}
