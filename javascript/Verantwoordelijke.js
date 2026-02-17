function VerwijderProduct(id) {
    if (!confirm("Weet je zeker dat je dit product wilt verwijderen?")) return;

    fetch("backend/verwijder_product.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id
    })
    .then(res => res.text())
    .then(data => {
        if (data === "OK") {
            location.reload();
        } else {
            alert("Verwijderen mislukt");
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.querySelector(".Toevoegen");
    if (!btn) return;

    btn.addEventListener("click", () => {
        const product = prompt("Productnaam:");
        if (!product) return;

        const contact = prompt("Contactpersoon:");
        const afdeling = prompt("Afdeling:");

        fetch("backend/toevoegen_product.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `product=${encodeURIComponent(product)}&contact=${encodeURIComponent(contact)}&afdeling=${encodeURIComponent(afdeling)}`
        })
        .then(res => res.text())
        .then(data => {
            if (data === "OK") {
                location.reload();
            } else {
                alert("Toevoegen mislukt");
            }
        });
    });
});
