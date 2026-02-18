<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once "backend/config.php";

$gebruikerEmail = $_SESSION['email'];

// Haal alle afdelingen op waar deze gebruiker toegang toe heeft
$stmt = $conn->prepare("SELECT Afdeling FROM AfdelingEmails WHERE Email = ?");
$stmt->bind_param("s", $gebruikerEmail);
$stmt->execute();
$result = $stmt->get_result();

$afdelingen = [];
while ($row = $result->fetch_assoc()) {
    $afdelingen[] = $row['Afdeling'];
}

// 1. Geen afdelingen → forbidden
if (empty($afdelingen)) {
    header("Location: forbidden.php");
    exit;
}

// 2. Eén afdeling → direct doorsturen
if (count($afdelingen) === 1) {
    $afdeling = urlencode($afdelingen[0]);
    header("Location: Afdeling.php?naam=$afdeling");
    exit;
}

// 3. Meerdere afdelingen → toon keuzescherm
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Kies jouw afdeling</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .box h2 {
            margin-bottom: 20px;
        }
        .afdeling-btn {
            display: block;
            margin: 10px 0;
            padding: 12px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .afdeling-btn:hover {
            background: #556cd6;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Kies jouw afdeling</h2>

        <?php foreach ($afdelingen as $afd): ?>
            <a class="afdeling-btn" href="Afdeling.php?naam=<?= urlencode($afd) ?>">
                <?= htmlspecialchars($afd) ?>
            </a>
        <?php endforeach; ?>
    </div>
</body>
</html>
