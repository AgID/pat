/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */
window.addEventListener("DOMContentLoaded", () => {
    let menuButton = document.querySelectorAll(".menuButton")
    let menuPopup = document.getElementById("menu-popup")
    menuButton.forEach((e) => {
        e.addEventListener("click", () => {
            menuPopup.classList.toggle("active")
            if(menuPopup.classList.contains("active")) {
                menuPopup.setAttribute('aria-expanded', 'true');
            }
        })
    })

    document.getElementById('overlayMenu').addEventListener('click', function() {
        menuPopup.setAttribute('aria-expanded', 'false');
        menuPopup.classList.remove("active");
    })

    document.getElementById('closeMenuButton').addEventListener('click', function() {
        menuPopup.setAttribute('aria-expanded', 'false');
        menuPopup.classList.remove("active");
    })
})