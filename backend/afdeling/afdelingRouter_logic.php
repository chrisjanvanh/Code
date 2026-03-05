<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$gebruikerEmail = $_SESSION['email'];

/* ---------------------------------------------------
   1. Haal alle afdelingen op waar deze gebruiker toegang toe heeft
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT Afdeling FROM AfdelingEmails WHERE Email = ?");
$stmt->execute([$gebruikerEmail]);
$afdelingen = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* ---------------------------------------------------
   2. Geen afdelingen → forbidden
--------------------------------------------------- */
if (empty($afdelingen)) {
    header("Location: forbidden.php");
    exit;
}

/* ---------------------------------------------------
   3. Eén afdeling → direct doorsturen
--------------------------------------------------- */
if (count($afdelingen) === 1) {
    $afdeling = urlencode($afdelingen[0]);
    header("Location: Afdeling.php?afdeling=$afdeling");
    exit;
}
