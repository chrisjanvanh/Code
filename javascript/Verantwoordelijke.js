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

    // --- TOEVOEGEN ---
    const btn = document.querySelector(".Toevoegen");
    if (btn) {
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
    }

    // --- ENTER OPSLAAN ---
    document.querySelectorAll("input[id^='contact_'], input[id^='afdeling_']").forEach(input => {
        input.addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();

                const parts = this.id.split("_");
                const id = parts[1];

                const contact = document.getElementById("contact_" + id).value;
                const afdeling = document.getElementById("afdeling_" + id).value;

                fetch("backend/update_product.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `id=${id}&contact=${encodeURIComponent(contact)}&afdeling=${encodeURIComponent(afdeling)}`
                })
                .then(res => res.text())
                .then(data => {
                    if (data === "OK") {
                        this.style.backgroundColor = "#c8f7c5";
                        setTimeout(() => this.style.backgroundColor = "", 500);
                    } else {
                        alert("Opslaan mislukt");
                    }
                });
            }
        });
    });

});
