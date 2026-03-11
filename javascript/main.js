let lastScrollTop = 0;

window.addEventListener("scroll", function () {
    let st = window.pageYOffset || document.documentElement.scrollTop;

    if (st > lastScrollTop) {
        document.querySelector("header").classList.add("hide");   // scroll omlaag → verbergen
        document.querySelector("content").classList.add("hide");   // scroll omlaag → verbergen
    } else {
        document.querySelector("header").classList.remove("hide"); // scroll omhoog → tonen
        document.querySelector("content").classList.remove("hide");   // scroll omlaag → verbergen
    }

    lastScrollTop = st <= 0 ? 0 : st;
});


function toggleMenu() {
    const header = document.querySelector('header');
    header.classList.toggle('open');
}
