<?php
require_once __DIR__ . '/../../config2.php';

$bedrijf = $_GET['bedrijf'] ?? '';

$stmt = $pdo->prepare("SELECT Product FROM Product WHERE Bedrijf = ? ORDER BY Product ASC");
$stmt->execute([$bedrijf]);
$producten = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$producten) {
    echo "<p>Geen producten voor dit bedrijf.</p>";
    exit;
}

foreach ($producten as $p) {
    echo '<input type="checkbox" id="'.htmlspecialchars($p).'" name="'.htmlspecialchars($p).'">';
    echo '<label for="'.htmlspecialchars($p).'">'.htmlspecialchars($p).'</label><br>';
}
