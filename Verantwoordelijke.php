<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
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
    <header>
        <img src="img/menu.png" alt="Menu button" class="menu-button" set onclick="toggleMenu()">
        <img src="img/logo.svg" alt="VDL Groep Logo">
        <a href="index.php">Homepagina</a>
        <a href="HuidigeToegangen.php">Medewerkers</a>
        <a href="HardwareToewijzen.php"> Hardware</a>
        <a href="Verantwoordelijke.php" class="current">Producten</a>
        <div style="
            position: absolute;
            right: 20px;
            top: 15px;
        ">
            <a href="logout.php" 
            style="padding: 8px 15px; background: #e74c3c; color: white; 
                    border-radius: 5px; text-decoration: none;">
                Uitloggen
            </a>
        </div>

    </header>

    <div class="content">

        <?php
        require 'backend/config.php';
        $result = $conn->query("SELECT * FROM Product ORDER BY ID ASC");
        ?>

        <table>
            <tr>
                <th>Product</th>
                <th>Contactpersoon</th>
                <th>Afdeling</th>
                <th>Product verwijderen</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
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
            <?php endwhile; ?>

            <tr>
                <td colspan="4">
                    <button type="button" class="Toevoegen">Toevoegen product</button>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
