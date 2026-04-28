document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formBuscarCitas");

    const selectServicio = document.getElementById("id_servicio");
    const fechaInput = document.getElementById("fecha_disponible");

    const fieldServicio = document.getElementById("fieldServicioDisponible");
    const fieldFecha = document.getElementById("fieldFechaDisponible");

    const errorServicio = document.getElementById("errorServicioDisponible");
    const errorFecha = document.getElementById("errorFechaDisponible");

    const boxDescripcion = document.getElementById("descripcionServicioBox");
    const textoDescripcion = document.getElementById("descripcionServicioTexto");

    const textos = window.citasDisponiblesTextos || {};

    function hoyFormatoISO() {
        const hoy = new Date();
        const year = hoy.getFullYear();
        const month = String(hoy.getMonth() + 1).padStart(2, "0");
        const day = String(hoy.getDate()).padStart(2, "0");

        return `${year}-${month}-${day}`;
    }

    function mostrarError(fieldEl, errorEl, mensaje) {
        if (fieldEl) {
            fieldEl.classList.add("auth-field-error");
        }

        if (errorEl) {
            errorEl.textContent = mensaje;
        }
    }

    function limpiarError(fieldEl, errorEl) {
        if (fieldEl) {
            fieldEl.classList.remove("auth-field-error");
        }

        if (errorEl) {
            errorEl.textContent = "";
        }
    }

    function actualizarDescripcionServicio() {
        if (!selectServicio || !boxDescripcion || !textoDescripcion) {
            return;
        }

        const opcionSeleccionada = selectServicio.options[selectServicio.selectedIndex];
        const descripcion = opcionSeleccionada ? opcionSeleccionada.getAttribute("data-descripcion") : "";

        if (descripcion && descripcion.trim() !== "") {
            textoDescripcion.textContent = descripcion;
            boxDescripcion.style.display = "block";
        } else {
            textoDescripcion.textContent = "";
            boxDescripcion.style.display = "none";
        }
    }

    function validarServicio() {
        limpiarError(fieldServicio, errorServicio);

        if (!selectServicio || selectServicio.value === "") {
            mostrarError(
                fieldServicio,
                errorServicio,
                textos.requiredService || "Debes seleccionar un servicio."
            );
            return false;
        }

        return true;
    }

    function validarFecha() {
        limpiarError(fieldFecha, errorFecha);

        if (!fechaInput || fechaInput.value === "") {
            mostrarError(
                fieldFecha,
                errorFecha,
                textos.requiredDate || "Debes seleccionar una fecha."
            );
            return false;
        }

        if (fechaInput.value < hoyFormatoISO()) {
            mostrarError(
                fieldFecha,
                errorFecha,
                textos.pastDate || "La fecha no puede ser anterior a hoy."
            );
            return false;
        }

        return true;
    }

    if (selectServicio) {
        selectServicio.addEventListener("change", function () {
            actualizarDescripcionServicio();
            validarServicio();
        });

        actualizarDescripcionServicio();
    }

    if (fechaInput) {
        fechaInput.addEventListener("change", validarFecha);
        fechaInput.addEventListener("blur", validarFecha);
    }

    if (form) {
        form.addEventListener("submit", function (event) {
            const servicioOk = validarServicio();
            const fechaOk = validarFecha();

            if (!servicioOk || !fechaOk) {
                event.preventDefault();
            }
        });
    }
});