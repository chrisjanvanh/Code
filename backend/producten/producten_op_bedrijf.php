<?php
session_start();
require_once __DIR__ . '/../config2.php';

$gebruikerEmail = $_SESSION['email'];

// Bedrijven ophalen waar gebruiker toegang toe heeft
$stmt = $pdo->prepare("
    SELECT DISTINCT Bedrijf 
    FROM AfdelingEmails 
    WHERE Email = ? AND Afdeling = 'IT Contact'
");
$stmt->execute([$gebruikerEmail]);
$bedrijvenUser = $stmt->fetchAll(PDO::FETCH_COLUMN);

$bedrijf = $_GET['bedrijf'] ?? '';

if (!in_array($bedrijf, $bedrijvenUser)) {
    die("<p>Geen toegang tot dit bedrijf.</p>");
}

$stmt = $pdo->prepare("SELECT * FROM Product WHERE Bedrijf = ? ORDER BY ID ASC");
$stmt->execute([$bedrijf]);
$producten = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$producten) {
    echo "<p>Geen producten gevonden voor dit bedrijf.</p>";
    exit;
}

echo '<br><br>
    <table>
        <tr>
            <th>Product</th>
            <th>Contactpersoon</th>
            <th>Afdeling</th>
            <th>Product verwijderen</th>
        </tr>';

foreach ($producten as $row) {
    echo "<tr>
            <td>" . htmlspecialchars($row['Product']) . "</td>

            <td>
                <input type='text'
                       id='contact_" . $row['ID'] . "'
                       value='" . htmlspecialchars($row['Contactpersoon']) . "'>
            </td>

            <td>
                <input type='text'
                       id='afdeling_" . $row['ID'] . "'
                       value='" . htmlspecialchars($row['Afdeling']) . "'>
            </td>

            <td>
                <button onclick='VerwijderProduct(" . $row['ID'] . ")'
                        class='button'>Verwijder</button>
            </td>
        </tr>";
}

echo "<tr>
        <td colspan='4'>
            <button type='button' class='Toevoegen'>Toevoegen product</button>
        </td>
      </tr>
    </table>";
