function VerwijderProduct(id) {
    if (!confirm("Weet je zeker dat je dit product wilt verwijderen?")) return;

    fetch("/backend/producten/producten.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=delete&id=${id}`
    })
    .then(res => res.text())
    .then(data => {
        if (data === "OK") location.reload();
        else alert("Verwijderen mislukt");
    });
}


document.addEventListener("DOMContentLoaded", () => {

    // --- PRODUCT TOEVOEGEN ---
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("Toevoegen")) {

            const product = prompt("Productnaam:");
            if (!product) return;

            const contact = prompt("Contactpersoon:");
            const afdeling = prompt("Afdeling:");
            const bedrijf = document.getElementById("bedrijf").value;

            fetch("/backend/producten/producten.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=add&product=${encodeURIComponent(product)}&contact=${encodeURIComponent(contact)}&afdeling=${encodeURIComponent(afdeling)}&bedrijf=${encodeURIComponent(bedrijf)}`
            })
            .then(res => res.text())
            .then(data => {
                if (data === "OK") {
                    laadProducten(); // direct verversen zonder reload
                } else {
                    alert("Toevoegen mislukt");
                }
            });
        }
    });


    // --- ENTER OPSLAAN ---
    document.addEventListener("keydown", function(e) {
        const target = e.target;

        // Alleen inputs die beginnen met contact_ of afdeling_
        if (target.id.startsWith("contact_") || target.id.startsWith("afdeling_")) {

            if (e.key === "Enter") {
                e.preventDefault();

                const parts = target.id.split("_");
                const id = parts[1];

                const contact = document.getElementById("contact_" + id).value;
                const afdeling = document.getElementById("afdeling_" + id).value;

                fetch("/backend/producten/producten.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `action=update&id=${id}&contact=${encodeURIComponent(contact)}&afdeling=${encodeURIComponent(afdeling)}&bedrijf=${encodeURIComponent(bedrijf)}`
                })
                .then(res => res.text())
                .then(data => {
                    if (data === "OK") {
                        target.style.backgroundColor = "green";
                        setTimeout(() => target.style.backgroundColor = "", 1000);
                    } else {
                        alert("Opslaan mislukt");
                    }
                });
            }
        }
    });

});


function laadProducten() {
    const bedrijf = document.getElementById("bedrijf").value;

    if (bedrijf.trim() === "") {
        document.getElementById("producten-container").innerHTML = "";
        return;
    }

    fetch("backend/producten/producten_op_bedrijf.php?bedrijf=" + encodeURIComponent(bedrijf))
        .then(response => response.text())
        .then(html => {
            document.getElementById("producten-container").innerHTML = html;
        });
}
