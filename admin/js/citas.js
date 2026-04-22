function abrirModalEditarHorario(id, dia, horaInicio, horaFin, activo) {
    document.getElementById("editar_id_horario").value = id;
    document.getElementById("editar_dia_semana").value = dia;
    document.getElementById("editar_hora_inicio").value = horaInicio;
    document.getElementById("editar_hora_fin").value = horaFin;
    document.getElementById("editar_activo").value = activo;
    document.getElementById("modalEditarHorario").style.display = "flex";
}

function cerrarModalEditarHorario() {
    document.getElementById("modalEditarHorario").style.display = "none";
}

function abrirModalEditarBloqueo(id, fecha, horaInicio, horaFin, motivo) {
    document.getElementById("editar_id_bloqueo").value = id;
    document.getElementById("editar_fecha_bloqueo").value = fecha;
    document.getElementById("editar_hora_inicio_bloqueo").value = horaInicio;
    document.getElementById("editar_hora_fin_bloqueo").value = horaFin;
    document.getElementById("editar_motivo_bloqueo").value = motivo;
    document.getElementById("modalEditarBloqueo").style.display = "flex";
}

function cerrarModalEditarBloqueo() {
    document.getElementById("modalEditarBloqueo").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const hoy = new Date().toISOString().split("T")[0];

    const formCrear = document.getElementById("formCrearBloqueo");
    const fechaCrear = document.getElementById("fecha_bloqueo");
    const horaInicioCrear = document.getElementById("hora_inicio_bloqueo");
    const horaFinCrear = document.getElementById("hora_fin_bloqueo");
    const motivoCrear = document.getElementById("motivo_bloqueo");

    const errorFechaCrear = document.getElementById("errorCrearBloqueoFecha");
    const errorHorasCrear = document.getElementById("errorCrearBloqueoHoras");
    const errorMotivoCrear = document.getElementById("errorCrearBloqueoMotivo");

    const fieldFechaCrear = document.getElementById("crearBloqueoFechaField");
    const fieldMotivoCrear = document.getElementById("crearBloqueoMotivoField");

    const formEditar = document.getElementById("formEditarBloqueo");
    const fechaEditar = document.getElementById("editar_fecha_bloqueo");
    const horaInicioEditar = document.getElementById("editar_hora_inicio_bloqueo");
    const horaFinEditar = document.getElementById("editar_hora_fin_bloqueo");
    const motivoEditar = document.getElementById("editar_motivo_bloqueo");

    const errorFechaEditar = document.getElementById("errorEditarBloqueoFecha");
    const errorHorasEditar = document.getElementById("errorEditarBloqueoHoras");
    const errorMotivoEditar = document.getElementById("errorEditarBloqueoMotivo");

    const fieldFechaEditar = document.getElementById("editarBloqueoFechaField");
    const fieldMotivoEditar = document.getElementById("editarBloqueoMotivoField");

    function limpiarError(errorEl, fieldEl) {
        if (errorEl) {
            errorEl.textContent = "";
        }
        if (fieldEl) {
            fieldEl.classList.remove("auth-field-error");
        }
    }

    function mostrarError(errorEl, fieldEl, mensaje) {
        if (errorEl) {
            errorEl.textContent = mensaje;
        }
        if (fieldEl) {
            fieldEl.classList.add("auth-field-error");
        }
    }

    function limpiarErrorSimple(errorEl) {
        if (errorEl) {
            errorEl.textContent = "";
        }
    }

    function mostrarErrorSimple(errorEl, mensaje) {
        if (errorEl) {
            errorEl.textContent = mensaje;
        }
    }

    function validarFechaNoPasada(valor, errorEl, fieldEl) {
        limpiarError(errorEl, fieldEl);

        if (!valor) {
            mostrarError(errorEl, fieldEl, "La fecha es obligatoria.");
            return false;
        }

        if (valor < hoy) {
            mostrarError(errorEl, fieldEl, "No se puede poner un bloqueo con fecha pasada.");
            return false;
        }

        return true;
    }

    function validarMotivoObligatorio(valor, errorEl, fieldEl) {
        limpiarError(errorEl, fieldEl);

        if (!valor || !valor.trim()) {
            mostrarError(errorEl, fieldEl, "El motivo es obligatorio.");
            return false;
        }

        return true;
    }

    function validarHorasBloqueo(horaInicioInput, horaFinInput, errorEl) {
        limpiarErrorSimple(errorEl);

        const horaInicio = horaInicioInput ? horaInicioInput.value.trim() : "";
        const horaFin = horaFinInput ? horaFinInput.value.trim() : "";

        if (horaInicio === "" && horaFin === "") {
            return true;
        }

        if ((horaInicio !== "" && horaFin === "") || (horaInicio === "" && horaFin !== "")) {
            mostrarErrorSimple(errorEl, "Debes indicar ambas horas o dejar ambas vacías para bloquear el día completo.");
            return false;
        }

        if (horaInicio >= horaFin) {
            mostrarErrorSimple(errorEl, "La hora de inicio debe ser menor que la hora de fin.");
            return false;
        }

        return true;
    }

    if (fechaCrear) {
        fechaCrear.addEventListener("change", function () {
            validarFechaNoPasada(fechaCrear.value, errorFechaCrear, fieldFechaCrear);
        });
    }

    if (motivoCrear) {
        motivoCrear.addEventListener("input", function () {
            validarMotivoObligatorio(motivoCrear.value, errorMotivoCrear, fieldMotivoCrear);
        });
    }

    if (horaInicioCrear && horaFinCrear) {
        horaInicioCrear.addEventListener("input", function () {
            validarHorasBloqueo(horaInicioCrear, horaFinCrear, errorHorasCrear);
        });

        horaFinCrear.addEventListener("input", function () {
            validarHorasBloqueo(horaInicioCrear, horaFinCrear, errorHorasCrear);
        });
    }

    if (formCrear) {
        formCrear.addEventListener("submit", function (e) {
            const okFecha = validarFechaNoPasada(fechaCrear.value, errorFechaCrear, fieldFechaCrear);
            const okHoras = validarHorasBloqueo(horaInicioCrear, horaFinCrear, errorHorasCrear);
            const okMotivo = validarMotivoObligatorio(motivoCrear.value, errorMotivoCrear, fieldMotivoCrear);

            if (!okFecha || !okHoras || !okMotivo) {
                e.preventDefault();
            }
        });
    }

    if (fechaEditar) {
        fechaEditar.addEventListener("change", function () {
            validarFechaNoPasada(fechaEditar.value, errorFechaEditar, fieldFechaEditar);
        });
    }

    if (motivoEditar) {
        motivoEditar.addEventListener("input", function () {
            validarMotivoObligatorio(motivoEditar.value, errorMotivoEditar, fieldMotivoEditar);
        });
    }

    if (horaInicioEditar && horaFinEditar) {
        horaInicioEditar.addEventListener("input", function () {
            validarHorasBloqueo(horaInicioEditar, horaFinEditar, errorHorasEditar);
        });

        horaFinEditar.addEventListener("input", function () {
            validarHorasBloqueo(horaInicioEditar, horaFinEditar, errorHorasEditar);
        });
    }

    if (formEditar) {
        formEditar.addEventListener("submit", function (e) {
            const okFecha = validarFechaNoPasada(fechaEditar.value, errorFechaEditar, fieldFechaEditar);
            const okHoras = validarHorasBloqueo(horaInicioEditar, horaFinEditar, errorHorasEditar);
            const okMotivo = validarMotivoObligatorio(motivoEditar.value, errorMotivoEditar, fieldMotivoEditar);

            if (!okFecha || !okHoras || !okMotivo) {
                e.preventDefault();
            }
        });
    }
});