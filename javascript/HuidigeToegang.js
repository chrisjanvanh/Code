function BedrijfVerlaat() {
    return confirm("Weet je zeker dat de medewerker het bedrijf verlaat? \nDeze actie kan niet ongedaan worden gemaakt.");
}

function naamtoevoegen() {
    const naam = document.getElementById("naamtoegangen")?.value;
    if (naam) {
        localStorage.setItem("naamNieuweMedewerker", naam);
    }
}
