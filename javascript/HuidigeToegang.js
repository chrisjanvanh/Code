function BedrijfVerlaat() {
    return confirm("Weet je zeker dat de medewerker het bedrijf verlaat? \nDeze actie kan niet ongedaan worden gemaakt.");
}

function naamtoevoegen() {
    const naam = document.getElementById("naamtoegangen")?.value;
    if (naam) {
        localStorage.setItem("naamNieuweMedewerker", naam);
    }
}

function verwijderMedewerker(naam) {
    if (!confirm(`Weet je zeker dat je ${naam} wilt verwijderen? \nDeze actie kan niet ongedaan worden gemaakt.`)) return;
     window.location.href = `backend/medewerker/verwijder_medewerker.php?naam=${encodeURIComponent(naam)}`;
}