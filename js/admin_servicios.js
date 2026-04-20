function abrirModalEditarServicio(id, nombre, duracion, precio) {
    document.getElementById("editar_id_servicio").value = id;
    document.getElementById("editar_nombre").value = nombre;
    document.getElementById("editar_duracion").value = duracion;
    document.getElementById("editar_precio").value = precio;
    document.getElementById("modalEditarServicio").classList.add("activo");
}

function cerrarModalEditarServicio() {
    document.getElementById("modalEditarServicio").classList.remove("activo");
}

function abrirModalEliminarServicio(id, nombre) {
    document.getElementById("eliminar_id_servicio").value = id;
    document.getElementById("textoModalEliminarServicio").innerHTML =
        '¿Seguro que quieres eliminar el servicio <strong>' + nombre + '</strong>?';
    document.getElementById("modalEliminarServicio").classList.add("activo");
}

function cerrarModalEliminarServicio() {
    document.getElementById("modalEliminarServicio").classList.remove("activo");
}

window.addEventListener("click", function (e) {
    const modalEditar = document.getElementById("modalEditarServicio");
    const modalEliminar = document.getElementById("modalEliminarServicio");

    if (modalEditar && e.target === modalEditar) {
        cerrarModalEditarServicio();
    }

    if (modalEliminar && e.target === modalEliminar) {
        cerrarModalEliminarServicio();
    }
});