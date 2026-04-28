function escaparHtml(texto) {
    return String(texto)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function reemplazarTexto(plantilla, datos) {
    let texto = plantilla || "";

    Object.keys(datos).forEach(function (clave) {
        texto = texto.replaceAll("{" + clave + "}", datos[clave]);
    });

    return texto;
}

function abrirModalReservar(idCita, fecha, hora, servicio) {
    const input = document.getElementById("inputIdCitaReservar");
    const texto = document.getElementById("textoModalReservar");
    const modal = document.getElementById("modalReservar");

    if (!input || !texto || !modal) return;

    input.value = idCita;

    texto.innerHTML = reemplazarTexto(window.modalesTextos.bookAppointmentText, {
        servicio: "<strong>" + escaparHtml(servicio) + "</strong>",
        fecha: "<strong>" + escaparHtml(fecha) + "</strong>",
        hora: "<strong>" + escaparHtml(hora) + "</strong>"
    });

    modal.classList.add("activo");
}

function cerrarModalReservar() {
    const modal = document.getElementById("modalReservar");

    if (modal) {
        modal.classList.remove("activo");
    }
}

function abrirModalAnular(idCita, fecha, hora, servicio) {
    const input = document.getElementById("inputIdCitaAnular");
    const texto = document.getElementById("textoModalAnular");
    const modal = document.getElementById("modalAnular");

    if (!input || !texto || !modal) return;

    input.value = idCita;

    texto.innerHTML = reemplazarTexto(window.modalesTextos.cancelAppointmentText, {
        servicio: "<strong>" + escaparHtml(servicio) + "</strong>",
        fecha: "<strong>" + escaparHtml(fecha) + "</strong>",
        hora: "<strong>" + escaparHtml(hora) + "</strong>"
    });

    modal.classList.add("activo");
}

function cerrarModalAnular() {
    const modal = document.getElementById("modalAnular");

    if (modal) {
        modal.classList.remove("activo");
    }
}

window.addEventListener("click", function (e) {
    const modalReservar = document.getElementById("modalReservar");
    const modalAnular = document.getElementById("modalAnular");

    if (modalReservar && e.target === modalReservar) {
        cerrarModalReservar();
    }

    if (modalAnular && e.target === modalAnular) {
        cerrarModalAnular();
    }
});