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
        setTimeout(() => inputElement.style.backgroundColor = "", 800);
    });
}
