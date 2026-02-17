function naamzoeken() {
  var naamInput, serienummerInput, naamFilter, serienummerFilter, table, tr, td, i, naamValue, serienummerValue;
  naamInput = document.getElementById("naam");
  serienummerInput = document.getElementById("serienummer");
  naamFilter = naamInput.value.toUpperCase();
  serienummerFilter = serienummerInput.value.toUpperCase();
  table = document.getElementById("Toegewezen");
  tr = table.getElementsByTagName("tr");
  
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td");
    if (td.length > 1) {
      naamValue = (td[0].textContent || td[0].innerText).toUpperCase();
      serienummerValue = (td[1].textContent || td[1].innerText).toUpperCase();
      
      var naamMatch = naamFilter === "" || naamValue.indexOf(naamFilter) > -1;
      var serienummerMatch = serienummerFilter === "" || serienummerValue.indexOf(serienummerFilter) > -1;
      
      if (naamMatch && serienummerMatch) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}