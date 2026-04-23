function abrirModalEditarServicio(id, nombre, descripcion, duracion, precio) {
    document.getElementById("editar_id_servicio").value = id;
    document.getElementById("editar_nombre").value = nombre;
    document.getElementById("editar_descripcion").value = descripcion || "";
    document.getElementById("editar_duracion").value = duracion;
    document.getElementById("editar_precio").value = precio;
    document.getElementById("modalEditarServicio").classList.add("activo");
}

function cerrarModalEditarServicio() {
    document.getElementById("modalEditarServicio").classList.remove("activo");
}

function abrirModalDesactivarServicio(id, nombre) {
    document.getElementById("desactivar_id_servicio").value = id;
    document.getElementById("textoModalDesactivarServicio").innerHTML =
        '¿Seguro que quieres desactivar el servicio <strong>' + nombre + '</strong>?';
    document.getElementById("modalDesactivarServicio").classList.add("activo");
}

function cerrarModalDesactivarServicio() {
    document.getElementById("modalDesactivarServicio").classList.remove("activo");
}

window.addEventListener("click", function (e) {
    const modalEditar = document.getElementById("modalEditarServicio");
    const modalDesactivar = document.getElementById("modalDesactivarServicio");

    if (modalEditar && e.target === modalEditar) {
        cerrarModalEditarServicio();
    }

    if (modalDesactivar && e.target === modalDesactivar) {
        cerrarModalDesactivarServicio();
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const botonesEditar = document.querySelectorAll(".btn-editar-servicio");
    const botonesDesactivar = document.querySelectorAll(".btn-desactivar-servicio");

    botonesEditar.forEach(function (boton) {
        boton.addEventListener("click", function () {
            abrirModalEditarServicio(
                boton.dataset.idServicio,
                boton.dataset.nombre,
                boton.dataset.descripcion,
                boton.dataset.duracion,
                boton.dataset.precio
            );
        });
    });

    botonesDesactivar.forEach(function (boton) {
        boton.addEventListener("click", function () {
            abrirModalDesactivarServicio(
                boton.dataset.idServicio,
                boton.dataset.nombre
            );
        });
    });

    const configuraciones = [
        {
            formId: "formCrearServicio",
            campos: [
                { inputId: "nombre", fieldId: "field_nombre", errorId: "error_nombre", tipo: "texto" },
                { inputId: "descripcion", fieldId: "field_descripcion", errorId: "error_descripcion", tipo: "texto" },
                { inputId: "duracion", fieldId: "field_duracion", errorId: "error_duracion", tipo: "numero_positivo" },
                { inputId: "precio", fieldId: "field_precio", errorId: "error_precio", tipo: "decimal_positivo" }
            ]
        },
        {
            formId: "formEditarServicio",
            campos: [
                { inputId: "editar_nombre", fieldId: "field_editar_nombre", errorId: "error_editar_nombre", tipo: "texto" },
                { inputId: "editar_descripcion", fieldId: "field_editar_descripcion", errorId: "error_editar_descripcion", tipo: "texto" },
                { inputId: "editar_duracion", fieldId: "field_editar_duracion", errorId: "error_editar_duracion", tipo: "numero_positivo" },
                { inputId: "editar_precio", fieldId: "field_editar_precio", errorId: "error_editar_precio", tipo: "decimal_positivo" }
            ]
        }
    ];

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

    function validarCampo(inputEl, fieldEl, errorEl, tipo) {
        const valor = inputEl.value.trim();

        limpiarError(fieldEl, errorEl);

        if (tipo === "texto") {
            if (!valor) {
                mostrarError(fieldEl, errorEl, "Este campo es obligatorio.");
                return false;
            }
            return true;
        }

        if (tipo === "numero_positivo") {
            if (!valor) {
                mostrarError(fieldEl, errorEl, "Este campo es obligatorio.");
                return false;
            }

            if (Number(valor) <= 0 || !Number.isInteger(Number(valor))) {
                mostrarError(fieldEl, errorEl, "Introduce un número entero mayor que 0.");
                return false;
            }

            return true;
        }

        if (tipo === "decimal_positivo") {
            if (!valor) {
                mostrarError(fieldEl, errorEl, "Este campo es obligatorio.");
                return false;
            }

            if (Number(valor) <= 0) {
                mostrarError(fieldEl, errorEl, "Introduce un precio mayor que 0.");
                return false;
            }

            return true;
        }

        return true;
    }

    configuraciones.forEach(function (config) {
        const form = document.getElementById(config.formId);

        if (!form) {
            return;
        }

        config.campos.forEach(function (campo) {
            const inputEl = document.getElementById(campo.inputId);
            const fieldEl = document.getElementById(campo.fieldId);
            const errorEl = document.getElementById(campo.errorId);

            if (!inputEl) {
                return;
            }

            inputEl.addEventListener("input", function () {
                validarCampo(inputEl, fieldEl, errorEl, campo.tipo);
            });

            inputEl.addEventListener("blur", function () {
                validarCampo(inputEl, fieldEl, errorEl, campo.tipo);
            });
        });

        form.addEventListener("submit", function (e) {
            let valido = true;

            config.campos.forEach(function (campo) {
                const inputEl = document.getElementById(campo.inputId);
                const fieldEl = document.getElementById(campo.fieldId);
                const errorEl = document.getElementById(campo.errorId);

                if (!inputEl) {
                    return;
                }

                if (!validarCampo(inputEl, fieldEl, errorEl, campo.tipo)) {
                    valido = false;
                }
            });

            if (!valido) {
                e.preventDefault();
            }
        });
    });
});