document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");

  if (!form) {
    return;
  }

  const textos = window.registroTextos || {};

  const t = function (clave, defecto) {
    return textos[clave] || defecto;
  };

  const nombreInput = form.querySelector('input[name="nombre"]');
  const fechaInput = form.querySelector('input[name="fecha_nacimiento"]');
  const telefonoInput = form.querySelector('input[name="telefono"]');
  const emailInput = form.querySelector('input[name="email"]');
  const claveInput = form.querySelector('input[name="clave"]');
  const repetirClaveInput = form.querySelector('input[name="repetir_clave"]');

  function getFieldContainer(input) {
    return input.closest(".auth-input-group");
  }

  function getFieldBox(input) {
    const container = getFieldContainer(input);

    if (!container) {
      return null;
    }

    if (input.name === "fecha_nacimiento") {
      const fields = container.querySelectorAll(".auth-field");
      return fields.length > 1 ? fields[1] : fields[0];
    }

    return container.querySelector(".auth-field");
  }

  function getErrorBox(input) {
    const container = getFieldContainer(input);
    return container ? container.querySelector(`[data-error-for="${input.name}"]`) : null;
  }

  function setError(input, message) {
    const fieldBox = getFieldBox(input);
    const errorBox = getErrorBox(input);

    if (fieldBox) {
      fieldBox.classList.add("auth-field-error");
    }

    if (errorBox) {
      errorBox.textContent = message;
    }
  }

  function clearError(input) {
    const fieldBox = getFieldBox(input);
    const errorBox = getErrorBox(input);

    if (fieldBox) {
      fieldBox.classList.remove("auth-field-error");
    }

    if (errorBox) {
      errorBox.textContent = "";
    }
  }

  function validarNombre() {
    const value = nombreInput.value.trim();

    if (value === "") {
      setError(nombreInput, t("requiredField", "Este campo es obligatorio."));
      return false;
    }

    if (value.length < 2) {
      setError(nombreInput, t("shortName", "Debe tener al menos 2 caracteres."));
      return false;
    }

    clearError(nombreInput);
    return true;
  }

  function validarFecha() {
    const value = fechaInput.value.trim();

    if (value === "") {
      setError(fechaInput, t("requiredField", "Este campo es obligatorio."));
      return false;
    }

    const fecha = new Date(value);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    if (Number.isNaN(fecha.getTime())) {
      setError(fechaInput, t("invalidDate", "Introduce una fecha válida."));
      return false;
    }

    fecha.setHours(0, 0, 0, 0);

    if (fecha > hoy) {
      setError(fechaInput, t("futureDate", "La fecha no puede ser mayor que hoy."));
      return false;
    }

    clearError(fechaInput);
    return true;
  }

  function validarTelefono() {
    const value = telefonoInput.value.trim();

    if (value === "") {
      setError(telefonoInput, t("requiredField", "Este campo es obligatorio."));
      return false;
    }

    const soloNumeros = value.replace(/\D/g, "");

    if (soloNumeros !== value) {
      setError(telefonoInput, t("phoneOnlyNumbers", "El teléfono solo puede contener números."));
      return false;
    }

    if (!/^[6789]\d{8}$/.test(value)) {
      setError(telefonoInput, t("invalidPhone", "Debe tener 9 dígitos y empezar por 6, 7, 8 o 9."));
      return false;
    }

    clearError(telefonoInput);
    return true;
  }

  function validarEmail() {
    const value = emailInput.value.trim();

    if (value === "") {
      setError(emailInput, t("requiredField", "Este campo es obligatorio."));
      return false;
    }

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(value)) {
      setError(emailInput, t("invalidEmail", "Introduce un correo electrónico válido."));
      return false;
    }

    clearError(emailInput);
    return true;
  }

  function validarClave() {
    const value = claveInput.value.trim();

    if (value === "") {
      setError(claveInput, t("requiredField", "Este campo es obligatorio."));
      return false;
    }

    if (value.length < 4) {
      setError(claveInput, t("shortPassword", "La contraseña debe tener al menos 4 caracteres."));
      return false;
    }

    clearError(claveInput);
    return true;
  }

  function validarRepetirClave() {
    const value = repetirClaveInput.value.trim();

    if (value === "") {
      setError(repetirClaveInput, t("requiredField", "Este campo es obligatorio."));
      return false;
    }

    if (value !== claveInput.value.trim()) {
      setError(repetirClaveInput, t("passwordsDontMatch", "Las contraseñas no coinciden."));
      return false;
    }

    clearError(repetirClaveInput);
    return true;
  }

  telefonoInput.addEventListener("input", () => {
    telefonoInput.value = telefonoInput.value.replace(/\D/g, "").slice(0, 9);
    validarTelefono();
  });

  telefonoInput.addEventListener("blur", validarTelefono);

  fechaInput.addEventListener("change", validarFecha);
  fechaInput.addEventListener("blur", validarFecha);

  nombreInput.addEventListener("input", validarNombre);
  nombreInput.addEventListener("blur", validarNombre);

  emailInput.addEventListener("input", validarEmail);
  emailInput.addEventListener("blur", validarEmail);

  claveInput.addEventListener("input", () => {
    validarClave();
    if (repetirClaveInput.value.trim() !== "") {
      validarRepetirClave();
    }
  });

  claveInput.addEventListener("blur", validarClave);

  repetirClaveInput.addEventListener("input", validarRepetirClave);
  repetirClaveInput.addEventListener("blur", validarRepetirClave);

  form.addEventListener("submit", (event) => {
    const nombreOk = validarNombre();
    const fechaOk = validarFecha();
    const telefonoOk = validarTelefono();
    const emailOk = validarEmail();
    const claveOk = validarClave();
    const repetirClaveOk = validarRepetirClave();

    if (!nombreOk || !fechaOk || !telefonoOk || !emailOk || !claveOk || !repetirClaveOk) {
      event.preventDefault();
    }
  });
});