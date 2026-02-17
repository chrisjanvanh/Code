<?php
require 'config.php';

if ($conn->connect_error) {
    die("❌ Databaseverbinding mislukt: " . $conn->connect_error);
}

echo "✅ Verbonden met database!<br>";

// Test query
$result = $conn->query("SHOW TABLES");

if ($result) {
    echo "📦 Tabellen gevonden:<br>";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
} else {
    echo "⚠️ Query mislukt: " . $conn->error;
}
?>
