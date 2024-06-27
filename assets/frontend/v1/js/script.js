/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

jQuery(document).ready(function() {

    var swiper = new Swiper(".slider-cards", {
        navigation: {
            nextEl: ".slider-cards-next",
            prevEl: ".slider-cards-prev",
        },
    });

    var swiper = new Swiper(".slider-avvisi", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
          el: ".avvisi-pagination",
          clickable: true,
        },
        breakpoints: {
            // when window width is >= 768px
            768: {
              slidesPerView: 2,
            },
            // when window width is >= 992px
            992: {
              slidesPerView: 3,
            }
          }
      });

    /* COMPARSA HEADER */
        let headerFull = document.getElementById("header-full")
        let headerCompact = document.getElementById("header-compact")
        let headerFullHeight = headerFull.offsetHeight

        function checkScroll() {
            if(headerFull && headerCompact && headerFullHeight) {
                let windowY = window.scrollY;
                if (windowY >= headerFullHeight) {
                    headerCompact.classList.add("show")
                    document.body.style.paddingTop = headerFullHeight + "px";
                    headerFull.style.display = "none"
                } else {
                    headerCompact.classList.remove("show")
                    headerFull.style.display = "block"
                    document.body.style.paddingTop = "unset"
                }
            }
        }
          
        window.addEventListener('scroll', checkScroll);
    /* fine COMPARSA HEADER */
});