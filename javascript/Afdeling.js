function afronden(naam, kolom, waarde, bedrijf) {
    if (!confirm(`Weet je zeker dat je deze taak van ${naam} wilt afronden?`)) return;

    fetch("/backend/afdeling/afronden.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        credentials: "include",
        body: `naam=${encodeURIComponent(naam)}&kolom=${encodeURIComponent(kolom)}&waarde=${waarde}$bedrijf=${bedrijf}`
    })
    .then(r => r.text())
    .then(t => {
        if (t.trim() === "OK") location.reload();
        else alert("Fout: " + t);
    });
}

function verwijderEmail(id) {
    if (!confirm("Weet je zeker dat je dit e-mailadres wilt verwijderen?")) return;

    fetch("/backend/afdeling/afdeling_email_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        credentials: "include",
        body: `action=delete&id=${id}`
    })
    .then(r => r.text())
    .then(t => {
        if (t.trim() === "OK") location.reload();
        else alert("Fout: " + t);
    });
}

function voegEmailToe() {
    const email = prompt("Voer een e-mailadres in:");
    if (!email) return;

    fetch("/backend/afdeling/afdeling_email_action.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        credentials: "include",
        body: `action=add&email=${encodeURIComponent(email)}&afdeling=${encodeURIComponent(afdeling)}`
    })
    .then(r => r.text())
    .then(t => {
        if (t.trim() === "OK") location.reload();
        else alert("Fout: " + t);
    });
}
