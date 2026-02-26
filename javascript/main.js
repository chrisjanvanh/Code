var lastScrollTop = 0;

$(window).scroll(function () {
  
var st = $(this).scrollTop();
        if (st < lastScrollTop){
            $('header').slideDown();
        } else {
          $('header').slideUp();
        }
        lastScrollTop = st;
  })

function toggleMenu() {
    const header = document.querySelector('header');
    header.classList.toggle('open');
}
