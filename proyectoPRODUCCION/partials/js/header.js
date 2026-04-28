document.addEventListener("DOMContentLoaded", function () {
    const langSelector = document.getElementById("langSelector");
    const mensajeBox = document.getElementById("langMessage");
    const menuToggle = document.getElementById("menuToggle");
    const mainNav = document.getElementById("mainNav");

    if (menuToggle && mainNav) {
        
        mainNav.classList.remove("nav-open");
        
        menuToggle.addEventListener("click", function () {
            mainNav.classList.toggle("nav-open");
            menuToggle.classList.toggle("activo");
        });

        mainNav.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                mainNav.classList.remove("nav-open");
                menuToggle.classList.remove("activo");
            });
        });
    }

    if (!langSelector) {
        return;
    }

    const textos = window.headerTextos || {};

    langSelector.addEventListener("change", function () {
        const selectedLang = this.value;

        fetch("/cambiar_idioma.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "lang=" + encodeURIComponent(selectedLang)
        })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    location.reload();
                    return;
                }

                mostrarError();
            })
            .catch(() => {
                mostrarError();
            });
    });

    function mostrarError() {
        if (!mensajeBox) return;

        mensajeBox.textContent = textos.error || "Error changing language";
        mensajeBox.style.display = "block";

        setTimeout(() => {
            mensajeBox.style.display = "none";
        }, 3000);
    }
});