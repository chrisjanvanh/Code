<?php
session_start();

// 1. Productdefinities (vervanging + uitbreiding)
$producten = [
    "11" => ["naam" => "Desktop i5 SFF", "type" => "vervanging"],
    "12" => ["naam" => "Desktop i5 SFF", "type" => "uitbreiding"],

    "21" => ["naam" => "Desktop i5 MFF", "type" => "vervanging"],
    "22" => ["naam" => "Desktop i5 MFF", "type" => "uitbreiding"],

    "31" => ["naam" => "Laptop 13\"", "type" => "vervanging"],
    "32" => ["naam" => "Laptop 13\"", "type" => "uitbreiding"],

    "41" => ["naam" => "Laptop 15\"", "type" => "vervanging"],
    "42" => ["naam" => "Laptop 15\"", "type" => "uitbreiding"],

    "51" => ["naam" => "Workstation standaard", "type" => "vervanging"],
    "52" => ["naam" => "Workstation standaard", "type" => "uitbreiding"],

    "61" => ["naam" => "Workstation heavy", "type" => "vervanging"],
    "62" => ["naam" => "Workstation heavy", "type" => "uitbreiding"],

    "71" => ["naam" => "Mobiel workstation 16\" light", "type" => "vervanging"],
    "72" => ["naam" => "Mobiel workstation 16\" light", "type" => "uitbreiding"],

    "81" => ["naam" => "Mobiel workstation 16\" heavy", "type" => "vervanging"],
    "82" => ["naam" => "Mobiel workstation 16\" heavy", "type" => "uitbreiding"],

    "91"  => ["naam" => "Monitor 23\"", "type" => "vervanging"],
    "92"  => ["naam" => "Monitor 23\"", "type" => "uitbreiding"],

    "101" => ["naam" => "Monitor 24\"", "type" => "vervanging"],
    "102" => ["naam" => "Monitor 24\"", "type" => "uitbreiding"],

    "111" => ["naam" => "Monitor 27\"", "type" => "vervanging"],
    "112" => ["naam" => "Monitor 27\"", "type" => "uitbreiding"],

    "121" => ["naam" => "Toetsenbord", "type" => "vervanging"],
    "122" => ["naam" => "Toetsenbord", "type" => "uitbreiding"],

    "131" => ["naam" => "Muis", "type" => "vervanging"],
    "132" => ["naam" => "Muis", "type" => "uitbreiding"],

    "141" => ["naam" => "Wireless muis", "type" => "vervanging"],
    "142" => ["naam" => "Wireless muis", "type" => "uitbreiding"],

    "151" => ["naam" => "Wireless keyb/muis", "type" => "vervanging"],
    "152" => ["naam" => "Wireless keyb/muis", "type" => "uitbreiding"],

    "161" => ["naam" => "Laptoptas 15\"", "type" => "vervanging"],
    "162" => ["naam" => "Laptoptas 15\"", "type" => "uitbreiding"],

    "171" => ["naam" => "Beveiligingskabelslot", "type" => "vervanging"],
    "172" => ["naam" => "Beveiligingskabelslot", "type" => "uitbreiding"],

    "181" => ["naam" => "Docking laptop", "type" => "vervanging"],
    "182" => ["naam" => "Docking laptop", "type" => "uitbreiding"],

    "191" => ["naam" => "Docking workstation", "type" => "vervanging"],
    "192" => ["naam" => "Docking workstation", "type" => "uitbreiding"],

    "201" => ["naam" => "Microsoft Surface GO", "type" => "vervanging"],
    "202" => ["naam" => "Microsoft Surface GO", "type" => "uitbreiding"],

    "211" => ["naam" => "Microsoft Surface PRO", "type" => "vervanging"],
    "212" => ["naam" => "Microsoft Surface PRO", "type" => "uitbreiding"],

    "221" => ["naam" => "Surface Go type cover", "type" => "vervanging"],
    "222" => ["naam" => "Surface Go type cover", "type" => "uitbreiding"],

    "231" => ["naam" => "Surface Pro keyboard", "type" => "vervanging"],
    "232" => ["naam" => "Surface Pro keyboard", "type" => "uitbreiding"],

    "241" => ["naam" => "Surface Slim Pen", "type" => "vervanging"],
    "242" => ["naam" => "Surface Slim Pen", "type" => "uitbreiding"],

    "251" => ["naam" => "Pro Sign. Keyboard + Slim Pen 2", "type" => "vervanging"],
    "252" => ["naam" => "Pro Sign. Keyboard + Slim Pen 2", "type" => "uitbreiding"],
];

// 2. Opsomming genereren
$opsomming = [];

foreach ($producten as $veld => $info) {
    $aantal = intval($_POST[$veld] ?? 0);

    if ($aantal > 0) {
        $opsomming[] = $aantal . "× " . $info['type'] . " " . $info['naam'];
    }
}

$opsomming = array_filter($opsomming); // ← verwijdert lege regels

$opsomming_tekst = implode("\n", $opsomming);

// 3. Webhook naar Make
$webhook_url = "https://hook.eu1.make.com/8jhm6yjjfthe6atw11qr3b87c38wdkqd";

$data = [
    "bedrijf" => $_POST["bedrijf"],
    "aanvrager" => $_POST["naamAanvrager"],
    "telefoonAanvrager" => $_POST["telefoonAanvrager"],
    "datum" => $_POST["datumAanvrager"],
    "investeringsnummer" => $_POST["investeringsnummer"],

    "voor_naam" => $_POST["naam"],
    "voor_afdeling" => $_POST["afdeling"],
    "voor_telefoon" => $_POST["telefoon"],

    "motivatie" => $_POST["motivatie"],
    "bestelling" => $opsomming_tekst
];

$ch = curl_init($webhook_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// 4. Redirect naar bedankpagina
header("Location: ../HardwareBestellen.php");
exit;
