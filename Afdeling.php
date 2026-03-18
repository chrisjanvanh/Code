<?php 
require 'backend/afdeling/afdeling_logic.php'; 
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VDL Bus & Coach</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/Afdeling.css">

    <script src="javascript/main.js" defer></script>
    <script src="javascript/Afdeling.js" defer></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'taken'); 
?>

<script>
    const afdeling = "<?= htmlspecialchars($afdeling) ?>";
</script>

<div class="content">
    <h1>Taken voor <?= htmlspecialchars($afdeling) ?></h1>

    <table>
        <tr>
            <th>Product</th>
            <th>Medewerker</th>
            <th>Functie</th>
            <th>Locatie</th>
            <th>Leidinggevende</th>
            <th>Bedrijf</th>
            <th>Referentie</th>
            <th>Taak</th>
        </tr>

        <?php foreach ($producten as $product): ?>
            <?php
                $kolom = $product['Product'];

                // Medewerkers ophalen via PDO
                $stmtM = $pdo->prepare("
                    SELECT m.*
                    FROM BedrijfProduct bp
                    JOIN Medewerker m ON m.Naam = bp.Medewerker
                    WHERE bp.Product = ?
                    AND bp.Bedrijf = ?
                    AND bp.Waarde IN (0,2)
                ");
                $stmtM->execute([$kolom, $bedrijf]);
                $medewerkers = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            ?>

            <?php foreach ($medewerkers as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($product['Product']) ?></td>
                    <td><?= htmlspecialchars($m['Naam']) ?></td>
                    <td><?= htmlspecialchars($m['Functie']) ?></td>
                    <td><?= htmlspecialchars($m['Locatie']) ?></td>
                    <td><?= htmlspecialchars($m['Leidinggevende']) ?></td>
                    <td><?= htmlspecialchars($m['Bedrijf']) ?></td>
                    <td><?= htmlspecialchars($m['Referentie']) ?></td>

                    <td>
                        <?php if ($m[$kolom] == 0): ?>
                            Toevoegen
                            <button class="button" onclick='afronden("<?= $m["Naam"] ?>", "<?= $kolom ?>", 1, "<?= $bedrijf ?>")'>Afronden</button>
                        <?php else: ?>
                            Verwijderen
                            <button class="button" onclick='afronden("<?= $m["Naam"] ?>", "<?= $kolom ?>", "null", "<?= $bedrijf ?>")'>Afronden</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>

    <br><br>

    <h2>Mailadressen voor deze afdeling</h2>

    <table>
        <tr>
            <th>Email</th>
            <th>Actie</th>
        </tr>

        <?php foreach ($emails as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['Email']) ?></td>
                <td>
                    <button class="button delete" onclick="verwijderEmail(<?= $e['ID'] ?>)">Verwijderen</button>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="2">
                <button class="button" onclick="voegEmailToe()">Email toevoegen</button>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
