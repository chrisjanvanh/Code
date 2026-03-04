document.addEventListener("DOMContentLoaded", () => {

    const checkboxes = document.querySelectorAll("input[type=checkbox]");
    const rows = document.querySelectorAll(".logrow");

    checkboxes.forEach(cb => {
        cb.addEventListener("change", () => {

            // Verzamel alle aangevinkte soorten
            const actieveFilters = [...checkboxes]
                .filter(c => c.checked)
                .map(c => c.value);

            rows.forEach(row => {
                const soort = row.dataset.soort;

                // Geen filters → alles tonen
                if (actieveFilters.length === 0) {
                    row.style.display = "";
                    return;
                }

                // Alleen tonen als soort in filters zit
                row.style.display = actieveFilters.includes(soort) ? "" : "none";
            });
        });
    });
});
