document.addEventListener("DOMContentLoaded", function () {
    const textos = window.perfilTextos || {};

    const t = function (clave, defecto) {
        return textos[clave] || defecto;
    };

    const form = document.getElementById("perfilForm");

    const nombre = document.getElementById("nombre");
    const fecha = document.getElementById("fecha_nacimiento");
    const telefono = document.getElementById("telefono");
    const email = document.getElementById("email");
    const clave = document.getElementById("clave");
    const repetirClave = document.getElementById("repetir_clave");

    const errorNombre = document.getElementById("errorNombre");
    const errorFecha = document.getElementById("errorFechaNacimiento");
    const errorTelefono = document.getElementById("errorTelefono");
    const errorEmail = document.getElementById("errorEmail");
    const errorClave = document.getElementById("errorClave");
    const errorRepetirClave = document.getElementById("errorRepetirClave");

    const fieldNombre = document.getElementById("nombreField");
    const fieldFecha = document.getElementById("fechaNacimientoField");
    const fieldTelefono = document.getElementById("telefonoField");
    const fieldEmail = document.getElementById("emailField");
    const fieldClave = document.getElementById("claveField");
    const fieldRepetirClave = document.getElementById("repetirClaveField");

    function limpiarError(errorEl, fieldEl) {
        if (errorEl) errorEl.textContent = "";
        if (fieldEl) fieldEl.classList.remove("auth-field-error");
    }

    function mostrarError(errorEl, fieldEl, mensaje) {
        if (errorEl) errorEl.textContent = mensaje;
        if (fieldEl) fieldEl.classList.add("auth-field-error");
    }

    function validarNombre() {
        limpiarError(errorNombre, fieldNombre);

        if (!nombre.value.trim()) {
            mostrarError(errorNombre, fieldNombre, t("requiredName", "El nombre es obligatorio."));
            return false;
        }

        if (nombre.value.trim().length < 2) {
            mostrarError(errorNombre, fieldNombre, t("shortName", "El nombre debe tener al menos 2 caracteres."));
            return false;
        }

        return true;
    }

    function validarFecha() {
        const hoy = new Date().toISOString().split("T")[0];
        limpiarError(errorFecha, fieldFecha);

        if (!fecha.value) {
            mostrarError(errorFecha, fieldFecha, t("requiredDate", "La fecha es obligatoria."));
            return false;
        }

        if (fecha.value > hoy) {
            mostrarError(errorFecha, fieldFecha, t("futureDate", "La fecha no puede ser futura."));
            return false;
        }

        return true;
    }

    function validarTelefono() {
        limpiarError(errorTelefono, fieldTelefono);

        const tel = telefono.value.trim();

        if (!tel) {
            mostrarError(errorTelefono, fieldTelefono, t("requiredPhone", "El teléfono es obligatorio."));
            return false;
        }

        if (!/^[6789]\d{8}$/.test(tel)) {
            mostrarError(errorTelefono, fieldTelefono, t("invalidPhone", "Debe ser un teléfono válido (9 dígitos y empezar por 6, 7, 8 o 9)."));
            return false;
        }

        return true;
    }

    function validarEmail() {
        limpiarError(errorEmail, fieldEmail);

        const valor = email.value.trim();

        if (!valor) {
            mostrarError(errorEmail, fieldEmail, t("requiredEmail", "El email es obligatorio."));
            return false;
        }

        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!regexEmail.test(valor)) {
            mostrarError(errorEmail, fieldEmail, t("invalidEmail", "Introduce un email válido."));
            return false;
        }

        return true;
    }

    function validarClave() {
        limpiarError(errorClave, fieldClave);

        const valorClave = clave.value.trim();
        const valorRepetir = repetirClave.value.trim();

        if (valorClave === "" && valorRepetir === "") return true;

        if (valorClave === "") {
            mostrarError(errorClave, fieldClave, t("requiredPassword", "Debes escribir la nueva contraseña."));
            return false;
        }

        if (valorClave.length < 4) {
            mostrarError(errorClave, fieldClave, t("shortPassword", "La contraseña debe tener al menos 4 caracteres."));
            return false;
        }

        return true;
    }

    function validarRepetirClave() {
        limpiarError(errorRepetirClave, fieldRepetirClave);

        const valorClave = clave.value.trim();
        const valorRepetir = repetirClave.value.trim();

        if (valorClave === "" && valorRepetir === "") return true;

        if (valorRepetir === "") {
            mostrarError(errorRepetirClave, fieldRepetirClave, t("requiredRepeatPassword", "Debes repetir la nueva contraseña."));
            return false;
        }

        if (valorClave !== valorRepetir) {
            mostrarError(errorRepetirClave, fieldRepetirClave, t("passwordsDontMatch", "Las contraseñas no coinciden."));
            return false;
        }

        return true;
    }

    nombre.addEventListener("input", validarNombre);
    fecha.addEventListener("change", validarFecha);

    telefono.addEventListener("input", function () {
        telefono.value = telefono.value.replace(/\D/g, "").slice(0, 9);
        validarTelefono();
    });

    email.addEventListener("input", validarEmail);

    clave.addEventListener("input", function () {
        validarClave();
        if (repetirClave.value.trim() !== "") validarRepetirClave();
    });

    repetirClave.addEventListener("input", validarRepetirClave);

    form.addEventListener("submit", function (e) {
        const okNombre = validarNombre();
        const okFecha = validarFecha();
        const okTelefono = validarTelefono();
        const okEmail = validarEmail();
        const okClave = validarClave();
        const okRepetirClave = validarRepetirClave();

        if (!okNombre || !okFecha || !okTelefono || !okEmail || !okClave || !okRepetirClave) {
            e.preventDefault();
        }
    });
});