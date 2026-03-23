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
    <link rel="stylesheet" href="css/HardwareBestellen.css">
    <script src="javascript/main.js"></script>
    <script src="javascript/HardwareBestellen.js"></script>

    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
</head>
<body>

<?php
require_once 'backend/auth/rechten.php';
toonMenu($rechten, 'index');
?>

    <div class="content">
        <form>
            Bedrijf:
            <input list="bedrijven" id="bedrijf" name="bedrijf" required oninput="laadProducten()">
            <datalist id="bedrijven">
                <option value="VDL Bus & Coach"></option>
            </datalist>
            <h3>Aanvrager:</h3>
            Naam:
            <input type="text" id="naamAanvrager" name="naamAanvrager" required>

            Datum:
            <input type="date" id="datumAanvrager" name="datumAanvrager" required>

            Telefoonnummer:
            <input type="tel" id="telefoonAanvrager" name="telefoonAanvrager" required>

            t.l.v. investeringsnummer:
            <input type="text" id="investeringsnummer" name="investeringsnummer">

            <h3>Voor:</h3>
            Naam:
            <input type="text" id="naam" name="naam" required>
            Afdeling:
            <input type="text" id="afdeling" name="afdeling" required>
            Telefoonnummer:
            <input type="tel" id="telefoon" name="telefoon" required>
            Bestaande gebruiker: <br>
            <label class="switch">
                <input type="checkbox">
                <span class="slider round"></span>
            </label> <br><br>
            Huidige computernaam indien van toepassing:
            <input type="text" id="huidig" name="huidig">

            <h3>Motivatie</h3>
            <input type="text" id="motivatie" name="motivatie">
            <br>

            <table>
                <tr>
                    <th>Product</th>
                    <th>Vervanging aantal</th>
                    <th>Uitbreiding aantal</th>
                    <th>Prijs/stuk</th>
                    <th>Totaalprijs</th>
                </tr>
                <tr>
                    <td>Desktop i5 SFF</td>
                    <td><input type="number" id="11" name="11"></td>
                    <td><input type="number" id="12" name="12"></td>
                    <td>€ 688,00</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Desktop i5 MFF</td>
                    <td><input type="number" id="21" name="21"></td>
                    <td><input type="number" id="22" name="22"></td>
                    <td>€ 645,00</td>
                    <td></td>
                </tr>
                <tr>
                    <td>laptop 13"</td>
                    <td><input type="number" id="31" name="31"></td>
                    <td><input type="number" id="32" name="32"></td>
                    <td>€ 827,31</td>
                    <td></td>
                </tr>
                <tr>
                    <td>laptop 15"</td>
                    <td><input type="number" id="41" name="41"></td>
                    <td><input type="number" id="42" name="42"></td>
                    <td>€ 862,85</td>
                    <td></td>
                </tr>
                <tr>
                    <td>workstation standaard</td>
                    <td><input type="number" id="51" name="51"></td>
                    <td><input type="number" id="52" name="52"></td>
                    <td>€ 760,30</td>
                    <td></td>
                </tr>
                <tr>
                    <td>workstation heavy</td>
                    <td><input type="number" id="61" name="61"></td>
                    <td><input type="number" id="62" name="62"></td>
                    <td>€ 2.516,85</td>
                    <td></td>
                </tr>
                <tr>
                    <td>mobiel workstation 16" light</td>
                    <td><input type="number" id="71" name="71"></td>
                    <td><input type="number" id="72" name="72"></td>
                    <td>€ 1.452,00</td>
                    <td></td>
                </tr>
                <tr>
                    <td>mobiel workstation 16" heavy</td>
                    <td><input type="number" id="81" name="81"></td>
                    <td><input type="number" id="82" name="82"></td>
                    <td>€ 2.269,00</td>
                    <td></td>
                </tr>
                <tr>
                    <td>monitor 23"</td>
                    <td><input type="number" id="91" name="91"></td>
                    <td><input type="number" id="92" name="92"></td>
                    <td>€ 153,69</td>
                    <td></td>
                </tr>
                <tr>
                    <td>monitor 24"</td>
                    <td><input type="number" id="101" name="101"></td>
                    <td><input type="number" id="102" name="102"></td>
                    <td>€ 168,20</td>
                    <td></td>
                </tr>
                <tr>
                    <td>monitor 27"</td>
                    <td><input type="number" id="111" name="111"></td>
                    <td><input type="number" id="112" name="112"></td>
                    <td>€ 183,31</td>
                    <td></td>
                </tr>
                <tr>
                    <td>toetsenbord</td>
                    <td><input type="number" id="121" name="121"></td>
                    <td><input type="number" id="122" name="122"></td>
                    <td>€ 14,76</td>
                    <td></td>
                </tr>
                <tr>
                    <td>muis</td>
                    <td><input type="number" id="131" name="131"></td>
                    <td><input type="number" id="132" name="132"></td>
                    <td>€ 6,81</td>
                    <td></td>
                </tr>
                <tr>
                    <td>wireless muis</td>
                    <td><input type="number" id="141" name="141"></td>
                    <td><input type="number" id="142" name="142"></td>
                    <td>€ 13,95</td>
                    <td></td>
                </tr>
                <tr>
                    <td>wireless keyb/muis</td>
                    <td><input type="number" id="151" name="151"></td>
                    <td><input type="number" id="152" name="152"></td>
                    <td>€ 22,48</td>
                    <td></td>
                </tr>
                <tr>
                    <td>laptoptas 15"</td>
                    <td><input type="number" id="161" name="161"></td>
                    <td><input type="number" id="162" name="162"></td>
                    <td>€ 27,10</td>
                    <td></td>
                </tr>
                <tr>
                    <td>beveiligingskabelslot </td>
                    <td><input type="number" id="171" name="171"></td>
                    <td><input type="number" id="172" name="172"></td>
                    <td>€ 23,83</td>
                    <td></td>
                </tr>
                <tr>
                    <td>docking laptop</td>
                    <td><input type="number" id="181" name="181"></td>
                    <td><input type="number" id="182" name="182"></td>
                    <td>€ 119,90</td>
                    <td></td>
                </tr>
                <tr>
                    <td>docking workstation</td>
                    <td><input type="number" id="191" name="191"></td>
                    <td><input type="number" id="192" name="192"></td>
                    <td>€ 183,60</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Microsoft Surface GO</td>
                    <td><input type="number" id="201" name="201"></td>
                    <td><input type="number" id="202" name="202"></td>
                    <td>€ 572,42</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Microsoft Surface PRO</td>
                    <td><input type="number" id="211" name="211"></td>
                    <td><input type="number" id="212" name="212"></td>
                    <td>€ 1.166,13</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Surface Go type cover</td>
                    <td><input type="number" id="221" name="221"></td>
                    <td><input type="number" id="222" name="222"></td>
                    <td>€ 70,09</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Surface Pro keyboard</td>
                    <td><input type="number" id="231" name="231"></td>
                    <td><input type="number" id="232" name="232"></td>
                    <td>€ 115,10</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Surface Slim Pen</td>
                    <td><input type="number" id="241" name="241"></td>
                    <td><input type="number" id="242" name="242"></td>
                    <td>€ 84,24</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Pro Sign. Keyboard+Slim Pen 2</td>
                    <td><input type="number" id="251" name="251"></td>
                    <td><input type="number" id="252" name="252"></td>
                    <td>€ 181,52</td>
                    <td></td>
                </tr>
                <tr style="height: 50px;">
                    <td colspan="4" style="text-align: right; font-weight: bold;">Eindtotaal:</td>
                    <td id="eindtotaal"></td>
                </tr>
                
            </table><br>
            <input type="submit" value="Versturen">
        </form>
    </div>
</body>
</html>