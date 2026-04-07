document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll("table tr");
    const eindtotaalCell = document.getElementById("eindtotaal");

    function updateEindtotaal() {
        let totaal = 0;

        rows.forEach(row => {
            const totaalCell = row.cells?.[4];
            if (!totaalCell) return;

            const text = totaalCell.textContent
                .replace("€", "")
                .replace(/\./g, "")
                .replace(",", ".")
                .trim();

            const waarde = parseFloat(text);
            if (!isNaN(waarde)) totaal += waarde;
        });

        eindtotaalCell.textContent = totaal > 0
            ? "€ " + totaal.toFixed(2).replace(".", ",")
            : "";
    }

    rows.forEach(row => {
        const inputs = row.querySelectorAll("input[type='number']");
        if (inputs.length === 0) return;

        const vervanging = inputs[0];
        const uitbreiding = inputs[1];
        const prijsCell = row.cells[3];
        const totaalCell = row.cells[4];

        // PRIJS UIT INPUTVELD HALEN
        const prijsInput = prijsCell.querySelector("input");

        function getPrijs() {
            return parseFloat(
                prijsInput.value
                    .replace(/\./g, "")
                    .replace(",", ".")
            ) || 0;
        }

        function updateTotaal() {
            const a = parseInt(vervanging.value) || 0;
            const b = parseInt(uitbreiding.value) || 0;
            const prijs = getPrijs();

            const totaal = (a + b) * prijs;

            totaalCell.textContent = totaal > 0
                ? "€ " + totaal.toFixed(2).replace(".", ",")
                : "";

            updateEindtotaal();
        }

        vervanging.addEventListener("input", updateTotaal);
        uitbreiding.addEventListener("input", updateTotaal);
        prijsInput.addEventListener("input", updateTotaal);
    });
});

function validatePrijs(waarde) {
    if (!/^[0-9.,]+$/.test(waarde)) {
        return false;
    }

    let dbWaarde = waarde.replace(/\./g, "").replace(",", ".");
    return !isNaN(parseFloat(dbWaarde));
}

function convertPrijsToDB(waarde) {
    waarde = waarde.trim();

    // Case 1: Europese notatie (12,50)
    if (waarde.includes(",")) {
        // alle duizendtallen verwijderen
        waarde = waarde.replace(/\./g, "");
        // komma → punt
        waarde = waarde.replace(",", ".");
        return waarde;
    }

    // Case 2: Engelse notatie (12.50 of 1.234.567.89)
    if (waarde.includes(".")) {
        // laatste punt is decimaalteken
        const lastDot = waarde.lastIndexOf(".");
        const before = waarde.substring(0, lastDot).replace(/\./g, "");
        const after = waarde.substring(lastDot + 1);
        return before + "." + after;
    }

    // Case 3: Hele getallen (12)
    return waarde;
}


function ToevoegenProduct() {
    const naam = prompt("Voer de productnaam in:");
    if (!naam) return;

    let prijs = prompt("Voer de prijs in (bijv. 12,50):");
    if (!prijs) return;

    if (!validatePrijs(prijs)) {
        alert("Ongeldig bedrag. Gebruik alleen cijfers, . of ,");
        return;
    }

    const prijsDB = convertPrijsToDB(prijs);

    fetch('backend/toevoegen_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'naam=' + encodeURIComponent(naam) + '&prijs=' + encodeURIComponent(prijsDB)
    })
    .then(r => r.text())
    .then(t => {
        if (t.trim() === "OK") {
            location.reload();
        } else {
            alert("Fout bij toevoegen: " + t);
        }
    });
}

