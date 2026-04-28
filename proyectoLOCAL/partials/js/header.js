document.addEventListener("DOMContentLoaded", function () {
    const langSelector = document.getElementById("langSelector");
    const mensajeBox = document.getElementById("langMessage");

    if (!langSelector) {
        return;
    }

    const textos = window.headerTextos || {};

    langSelector.addEventListener("change", function () {
        const selectedLang = this.value;

        fetch("/ProyectoDAW/cambiar_idioma.php", {
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

        // Se oculta solo después de 3 segundos
        setTimeout(() => {
            mensajeBox.style.display = "none";
        }, 3000);
    }
});