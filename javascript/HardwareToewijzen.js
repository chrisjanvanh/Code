function naamzoeken() {
    const naamFilter = document.getElementById("naam").value.toUpperCase();
    const serienummerFilter = document.getElementById("serienummer").value.toUpperCase();

    const table = document.getElementById("Toegewezen");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        const cols = rows[i].getElementsByTagName("td");
        if (cols.length > 1) {
            const naamValue = cols[0].innerText.toUpperCase();
            const serienummerValue = cols[1].innerText.toUpperCase();

            const naamMatch = naamFilter === "" || naamValue.includes(naamFilter);
            const serienummerMatch = serienummerFilter === "" || serienummerValue.includes(serienummerFilter);

            rows[i].style.display = (naamMatch && serienummerMatch) ? "" : "none";
        }
    }
}
