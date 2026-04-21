document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("perfilForm");

    const nombre = document.getElementById("nombre");
    const fecha = document.getElementById("fecha_nacimiento");
    const telefono = document.getElementById("telefono");
    const email = document.getElementById("email");

    const errorNombre = document.getElementById("errorNombre");
    const errorFecha = document.getElementById("errorFechaNacimiento");
    const errorTelefono = document.getElementById("errorTelefono");
    const errorEmail = document.getElementById("errorEmail");

    const fieldFecha = document.getElementById("fechaNacimientoField");

    function validarNombre() {
        errorNombre.textContent = "";
        if (!nombre.value.trim()) {
            errorNombre.textContent = "El nombre es obligatorio.";
            return false;
        }
        return true;
    }

    function validarFecha() {
        const hoy = new Date().toISOString().split("T")[0];
        errorFecha.textContent = "";
        fieldFecha.classList.remove("auth-field-error");

        if (!fecha.value) {
            errorFecha.textContent = "La fecha es obligatoria.";
            return false;
        }

        if (fecha.value > hoy) {
            errorFecha.textContent = "La fecha no puede ser futura.";
            fieldFecha.classList.add("auth-field-error");
            return false;
        }

        return true;
    }

    function validarTelefono() {
        errorTelefono.textContent = "";
        const tel = telefono.value.trim();

        if (!tel) {
            errorTelefono.textContent = "El teléfono es obligatorio.";
            return false;
        }

        if (!/^[6789]\d{8}$/.test(tel)) {
            errorTelefono.textContent = "Debe ser un teléfono válido (9 dígitos y empezar por 6, 7, 8 o 9).";
            return false;
        }

        return true;
    }

    function validarEmail() {
        errorEmail.textContent = "";

        if (!email.value.trim()) {
            errorEmail.textContent = "El email es obligatorio.";
            return false;
        }

        if (!email.checkValidity()) {
            errorEmail.textContent = "Introduce un email válido.";
            return false;
        }

        return true;
    }

    // Eventos en vivo
    nombre.addEventListener("input", validarNombre);
    fecha.addEventListener("change", validarFecha);
    telefono.addEventListener("input", validarTelefono);
    email.addEventListener("input", validarEmail);

    form.addEventListener("submit", function (e) {
        const ok =
            validarNombre() &
            validarFecha() &
            validarTelefono() &
            validarEmail();

        if (!ok) {
            e.preventDefault();
        }
    });

});