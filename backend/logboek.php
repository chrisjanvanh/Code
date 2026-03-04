<?php
require "config2.php";

$stmt = $pdo->prepare("
    SELECT Actie, Timestamp
    FROM Logboek
    ORDER BY Timestamp DESC
    LIMIT 50
");
$stmt->execute();

$actie = $stmt->fetchAll(PDO::FETCH_ASSOC);
