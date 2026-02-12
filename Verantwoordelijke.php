<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/Verantwoordelijke.css">
    <script src="javascript/auth.js"></script>
    <script src="javascript/Verantwoordelijke.js"></script>
    <script src="javascript/main.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>
    <header>
        <img src="img/menu.png" alt="Menu button" class="menu-button" set onclick="toggleMenu()">
        <img src="img/logo.svg" alt="VDL Groep Logo">
        <a href="index.php" class="current">Homepagina</a>
        <a href="HuidigeToegangen.php">Medewerkers</a>
        <a href="HardwareToewijzen.php"> Hardware</a>
        <a href="Verantwoordelijke.php" class="current">Producten</a>
    </header>

    <div class="content">
        <table>
            <tr>
                <th>Product</th>
                <th>Contactperson</th>
                <th>Afdeling</th>
                <th>Product verwijderen</th>
            </tr>
            <tr>
                <td>SAP</td>
                <td><input type="text" id="sap" name="sap" value="Antoine Henselmans"></td>
                <td><input type="text" id="sap" name="sap" value="Finance"></td>
                <td><button onclick="VerwijderProduct('SAP')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>CIP</td>
                <td><input type="text" id="cip" name="cip" value="Elisa Debourse"></td>
                <td><input type="text" id="cip" name="cip" value="Communicatie"></td>
                <td><button onclick="VerwijderProduct('CIP')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>CRM</td>
                <td><input type="text" id="crm" name="crm" value="Lambert van Gompel"></td>
                <td><input type="text" id="crm" name="crm" value="Business IT"></td>
                <td><button onclick="VerwijderProduct('CRM')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>CPQ</td>
                <td><input type="text" id="cpq" name="cpq" value="Sven de Vaal"></td>
                <td><input type="text" id="cpq" name="cpq" value="Business IT"></td>
                <td><button onclick="VerwijderProduct('CPQ')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>Power BI</td>
                <td><input type="text" id="powerbi" name="powerbi" value="Alexander van Vals"></td>
                <td><input type="text" id="powerbi" name="powerbi" value="Business IT"></td>
                <td><button onclick="VerwijderProduct('Power BI')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>VDL AD Account</td>
                <td><input type="text" id="vdladaccount" name="vdladaccount" value="Sven de Vaal"></td>
                <td><input type="text" id="vdladaccount" name="vdladaccount" value="Business IT"></td>
                <td><button onclick="VerwijderProduct('VDL AD Account')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>Rechten Netwerkschijf</td>
                <td><input type="text" id="rechtennetwerkschijf" name="rechtennetwerkschijf" value="Service Desk"></td>
                <td><input type="text" id="rechtennetwerkschijf" name="rechtennetwerkschijf" value="Service Desk"></td>
                <td><button onclick="VerwijderProduct('Rechten Netwerkschijf')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>MyVDL</td>
                <td><input type="text" id="myvdl" name="myvdl" value="Stefanie van Meijl"></td>
                <td><input type="text" id="myvdl" name="myvdl" value="HR"></td>
                <td><button onclick="VerwijderProduct('MyVDL')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>PLM Windchill</td>
                <td><input type="text" id="plmwindchill" name="plmwindchill" value="Rik Vanderper"></td>
                <td><input type="text" id="plmwindchill" name="plmwindchill" value="Engineering"></td>
                <td><button onclick="VerwijderProduct('PLM Windchill')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>Bedrijfsportal Access</td>
                <td><input type="text" id="bedrijfsportalaccess" name="bedrijfsportalaccess" value="Elisa Debourse"></td>
                <td><input type="text" id="bedrijfsportalaccess" name="bedrijfsportalaccess" value="Communicatie"></td>
                <td><button onclick="VerwijderProduct('Bedrijfsportal Access')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>IMS</td>
                <td><input type="text" id="ims" name="ims" value="Berrie Posthuma"></td>
                <td><input type="text" id="ims" name="ims" value="Quality"></td>
                <td><button onclick="VerwijderProduct('IMS')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>Laptop</td>
                <td><input type="text" id="laptop" name="laptop" value="Sven de Vaal"></td>
                <td><input type="text" id="laptop" name="laptop" value="Business IT"></td>
                <td><button onclick="VerwijderProduct('Laptop')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>Mobiele telefoon</td>
                <td><input type="text" id="mobiele_telefoon" name="mobiele_telefoon" value="Mariëlle van Keulen"></td>
                <td><input type="text" id="mobiele_telefoon" name="mobiele_telefoon" value="Management Assistent"></td>
                <td><button onclick="VerwijderProduct('Mobiele telefoon')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td>Muis en toetsenbord</td>
                <td><input type="text" id="muis_toetsenbord" name="muis_toetsenbord" value="Sven de Vaal"></td>
                <td><input type="text" id="muis_toetsenbord" name="muis_toetsenbord" value="Business IT"></td>
                <td><button onclick="VerwijderProduct('Muis en toetsenbord')" class="button">Verwijder</button></td>
            </tr>
            <tr>
                <td colspan="4"><button type="button" class="Toevoegen">Toevoegen product</button></td>
            </tr>
        </table>
    </div>
</body>
</html>