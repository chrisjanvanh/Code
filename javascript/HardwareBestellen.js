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
