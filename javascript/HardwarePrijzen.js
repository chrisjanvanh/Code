function updatePrijs(id, waarde, inputElement) {
    // Europese notatie → database notatie
    let prijs = waarde.replace(/\./g, '').replace(',', '.');

    fetch('backend/update_prijs.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id) + '&prijs=' + encodeURIComponent(prijs)
    })
    .then(r => r.text())
    .then(t => {
        // groene highlight
        inputElement.style.backgroundColor = "lightgreen";
        setTimeout(() => {
            inputElement.style.backgroundColor = "";
        }, 1000);
    })
    .catch(err => {
        inputElement.style.backgroundColor = "lightcoral";
        setTimeout(() => {
            inputElement.style.backgroundColor = "";
        }, 1000);
    });
}


function VerwijderProduct(id) {
    if (!confirm("Weet je zeker dat je dit product wilt verwijderen?")) {
        return;
    }

    fetch('backend/verwijder_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(r => r.text())
    .then(t => {
        if (t.trim() === "OK") {
            // Herlaad de pagina zodat de tabel wordt bijgewerkt
            location.reload();
        } else {
            alert("Fout bij verwijderen: " + t);
        }
    })
    .catch(err => {
        alert("Er ging iets mis bij de verbinding met de server");
    });
}
