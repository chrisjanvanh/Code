window.addEventListener("load", function() {
    const naamOpgeslagen = localStorage.getItem("naamNieuweMedewerker");
    if (naamOpgeslagen) {
        document.getElementById("naam").value = naamOpgeslagen;
        localStorage.removeItem("naamNieuweMedewerker");
    }
});

function laadProducten() {
    const bedrijf = document.getElementById("bedrijf").value;

    if (bedrijf.trim() === "") {
        document.getElementById("producten-container").innerHTML = "";
        return;
    }

    fetch("backend/medewerker/get_producten.php?bedrijf=" + encodeURIComponent(bedrijf))
        .then(res => res.text())
        .then(html => {
            document.getElementById("producten-container").innerHTML = html;
        });
}
