<?php
require 'config.php';

if (!isset($_POST['id'])) {
    http_response_code(400);
    echo "Geen ID ontvangen";
    exit;
}

$id = intval($_POST['id']);

$stmt = $conn->prepare("DELETE FROM Product WHERE ID = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "FOUT";
}

$stmt->close();
?>