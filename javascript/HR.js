function afronden(naam, kolom, waarde) {

    if (!confirm("Weet je zeker dat je deze taak van " + naam + " wilt afronden?")) return;

    fetch("backend/afronden.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "naam=" + encodeURIComponent(naam) +
              "&kolom=" + encodeURIComponent(kolom) +
              "&waarde=" + encodeURIComponent(waarde)
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "OK") {
            location.reload(); // pagina verversen zodat taak verdwijnt
        } else {
            alert("Fout bij afronden: " + data);
        }
    });
}

function verwijderEmail(id) {
    if (!confirm("Weet je zeker dat je dit e-mailadres wilt verwijderen?")) return;

    fetch("backend/EmailAfdeling.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=delete&id=" + encodeURIComponent(id)
    })
    .then(r => r.text())
    .then(t => {
        if (t.trim() === "OK") location.reload();
        else alert("Fout: " + t);
    });
}


function voegEmailToe() {
    const email = prompt("Voer een e-mailadres in dat je wilt toevoegen:");
    if (!email) return;

    fetch("backend/EmailAfdeling.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body:
            "action=add&email=" + encodeURIComponent(email) +
            "&afdeling=" + encodeURIComponent(afdeling)
    })
    .then(r => r.text())
    .then(t => {
        if (t.trim() === "OK") location.reload();
        else alert("Fout: " + t);
    });
}
