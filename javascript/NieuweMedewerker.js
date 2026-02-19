window.addEventListener("load", function() {
    const naamOpgeslagen = localStorage.getItem("naamNieuweMedewerker");
    if (naamOpgeslagen) {
        document.getElementById("naam").value = naamOpgeslagen;
        localStorage.removeItem("naamNieuweMedewerker");
    }
});
