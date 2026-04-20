function abrirModalEditarCita(id, fecha, hora, idServicio) {
    document.getElementById("editar_id_cita").value = id;
    document.getElementById("editar_fecha_cita").value = fecha;
    document.getElementById("editar_hora_cita").value = hora;
    document.getElementById("editar_servicio_cita").value = idServicio;
    document.getElementById("modalEditarCita").classList.add("activo");
}

function cerrarModalEditarCita() {
    document.getElementById("modalEditarCita").classList.remove("activo");
}

function abrirModalEliminarCita(id, fecha, hora) {
    document.getElementById("eliminar_id_cita").value = id;
    document.getElementById("textoModalEliminarCita").innerHTML =
        '¿Seguro que quieres eliminar la cita del día <strong>' + fecha + '</strong> a las <strong>' + hora + '</strong>?';
    document.getElementById("modalEliminarCita").classList.add("activo");
}

function cerrarModalEliminarCita() {
    document.getElementById("modalEliminarCita").classList.remove("activo");
}

window.addEventListener("click", function (e) {
    const modalEditar = document.getElementById("modalEditarCita");
    const modalEliminar = document.getElementById("modalEliminarCita");

    if (modalEditar && e.target === modalEditar) {
        cerrarModalEditarCita();
    }

    if (modalEliminar && e.target === modalEliminar) {
        cerrarModalEliminarCita();
    }
});