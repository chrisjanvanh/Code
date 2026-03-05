<?php
session_start();
require_once 'backend/config2.php';

// Alleen Business IT mag deze pagina zien
$gebruikerEmail = $_SESSION['email'];
$afdelingRecht = "Business IT";

$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdelingRecht, $gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: forbidden.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/Verantwoordelijke.css">
    <script src="javascript/Verantwoordelijke.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'producten');
?>

<div class="content">

    <?php
    // Producten ophalen via PDO
    $stmt = $pdo->query("SELECT * FROM Product ORDER BY ID ASC");
    $producten = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table>
        <tr>
            <th>Product</th>
            <th>Contactpersoon</th>
            <th>Afdeling</th>
            <th>Product verwijderen</th>
        </tr>

        <?php foreach ($producten as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Product']) ?></td>

                <td>
                    <input type="text"
                           id="contact_<?= $row['ID'] ?>"
                           value="<?= htmlspecialchars($row['Contactpersoon']) ?>">
                </td>

                <td>
                    <input type="text"
                           id="afdeling_<?= $row['ID'] ?>"
                           value="<?= htmlspecialchars($row['Afdeling']) ?>">
                </td>

                <td>
                    <button onclick="VerwijderProduct(<?= $row['ID'] ?>)"
                            class="button">Verwijder</button>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="4">
                <button type="button" class="Toevoegen">Toevoegen product</button>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
