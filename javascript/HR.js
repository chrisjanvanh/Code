function afronden(naam, kolom, waarde) {

    if (!confirm("Weet je zeker dat je deze taak wilt afronden?")) return;

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
