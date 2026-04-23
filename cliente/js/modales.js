function abrirModalReservar(idCita, fecha, hora, servicio) {
    const input = document.getElementById("inputIdCitaReservar");
    const texto = document.getElementById("textoModalReservar");
    const modal = document.getElementById("modalReservar");

    if (!input || !texto || !modal) return;

    input.value = idCita;
    texto.innerHTML =
        "Vas a reservar una cita de <strong>" + servicio + "</strong> el día <strong>" + fecha + "</strong> a las <strong>" + hora + "</strong>. ¿Deseas continuar?";
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
    texto.innerHTML =
        "¿Seguro que quieres anular tu cita de <strong>" + servicio + "</strong> el día <strong>" + fecha + "</strong> a las <strong>" + hora + "</strong>?";
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