$(document).ready(function(){
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({
            trigger: "hover",
        })
    })
    

    /* TOGGLE PULSANTI */
    const pulsanteVisibility = document.querySelectorAll(".pulsante-visibility");
    const pulsanteSearchEngine = document.querySelectorAll(".pulsante-search-engine");

    pulsanteVisibility.forEach(function(element) {
        element.addEventListener('click', function() {
            element.classList.toggle("active");
            if (element.classList.contains("active")) {
                element.childNodes[1].classList.remove("fa-users-slash");
                element.childNodes[1].classList.add("fa-users");
                element.ariaLabel = "Visibile"
                element.setAttribute("data-original-title", "Visibile");
            }
            else {
                element.childNodes[1].classList.remove("fa-users");
                element.childNodes[1].classList.add("fa-users-slash");
                element.ariaLabel = "Non visibile";
                element.setAttribute("data-original-title", "Non visibile");
            }
        });
    });

    pulsanteSearchEngine.forEach(function(element) {
        element.addEventListener('click', function() {
            element.classList.toggle("active");
            if (element.classList.contains("active")) {
                element.childNodes[1].classList.remove("fa-eye-slash");
                element.childNodes[1].classList.add("fa-eye");
                element.ariaLabel = "Indicizzato su motori di ricerca";
                element.setAttribute("data-original-title", "Indicizzato su motori di ricerca");
            }
            else {
                element.childNodes[1].classList.remove("fa-eye");
                element.childNodes[1].classList.add("fa-eye-slash");
                element.ariaLabel = "Non indicizzato su motori di ricerca"
                element.setAttribute("data-original-title", "Non indicizzato su motori di ricerca");
            }
        });
    });

    /* fine TOGGLE PULSANTI */

    /* DROPDOWN CARTELLE SIDEBAR */
    let sidebarFolder = document.querySelectorAll(".folder");
    sidebarFolder.forEach(function(element) {
        if (element.nextElementSibling) {
            element.nextElementSibling.style.display = "none";
        }
        element.addEventListener('click', function() {
            if (element.nextElementSibling) {
                if (element.getAttribute("aria-expanded") == "false") {
                    let nextFolder = element.nextElementSibling;
                    element.setAttribute("aria-expanded", "true");
                    nextFolder.style.display = "block";
                }
                else if (element.getAttribute("aria-expanded") == "true") {
                    let nextFolder = element.nextElementSibling;
                    element.setAttribute("aria-expanded", "false");
                    nextFolder.style.display = "none";
                }
            }
        });
    });
    /* fine DROPDOWN CARTELLE SIDEBAR */

    /* TOGGLE SIDEBAR */
    let sidebarButton = document.getElementById("sidebarButton");
    let closeSidebar = document.getElementById("closeSidebar");
    let sidebar = document.getElementById("sidebar");

    sidebarButton.addEventListener('click', function() {
        if (sidebarButton.getAttribute("aria-expanded") == "false") {
            sidebarButton.setAttribute("aria-label", "true");
            sidebar.classList.add("active");
        }
        else if (sidebarButton.getAttribute("aria-expanded") == "true") {
            sidebarButton.setAttribute("aria-label", "false");
            sidebar.classList.remove("active");
        }
    })

    closeSidebar.addEventListener('click', function() {
        sidebarButton.setAttribute("aria-label", "false");
        sidebar.classList.remove("active");
    })
    /* fine TOGGLE SIDEBAR */
});