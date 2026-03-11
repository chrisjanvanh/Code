let lastScrollTop = 0;

window.addEventListener("scroll", function () {
    let st = window.pageYOffset || document.documentElement.scrollTop;

    if (st > lastScrollTop) {
        document.querySelector("header").classList.add("hide");   // scroll omlaag → verbergen
        document.getElementById("hide").classList.add("hide");
    } else {
        document.querySelector("header").classList.remove("hide"); // scroll omhoog → tonen
        document.getElementById("hide").classList.remove("hide");

    }

    lastScrollTop = st <= 0 ? 0 : st;
});


function toggleMenu() {
    const header = document.querySelector('header');
    header.classList.toggle('open');
}
