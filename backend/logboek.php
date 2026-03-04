<?php
require "config2.php";

$stmt = $pdo->prepare("
    SELECT Actie, Soort, Timestamp
    FROM Logboek
    ORDER BY Timestamp DESC
");
$stmt->execute();

$actie = $stmt->fetchAll(PDO::FETCH_ASSOC);
