document.addEventListener("DOMContentLoaded", function () {
    const selectServicio = document.getElementById("id_servicio");
    const boxDescripcion = document.getElementById("descripcionServicioBox");
    const textoDescripcion = document.getElementById("descripcionServicioTexto");

    function actualizarDescripcionServicio() {
        if (!selectServicio) {
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

    if (selectServicio) {
        selectServicio.addEventListener("change", actualizarDescripcionServicio);
        actualizarDescripcionServicio();
    }
});