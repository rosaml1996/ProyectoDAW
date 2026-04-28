document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");

  if (!form) {
    return;
  }

  const textos = window.loginTextos || {};

  const t = function (clave, defecto) {
    return textos[clave] || defecto;
  };

  const emailInput = form.querySelector('input[name="email"]');
  const claveInput = form.querySelector('input[name="clave"]');

  function getFieldContainer(input) {
    return input.closest(".auth-input-group");
  }

  function getFieldBox(input) {
    const container = getFieldContainer(input);
    return container ? container.querySelector(".auth-field") : null;
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

    clearError(claveInput);
    return true;
  }

  emailInput.addEventListener("input", validarEmail);
  emailInput.addEventListener("blur", validarEmail);

  claveInput.addEventListener("input", validarClave);
  claveInput.addEventListener("blur", validarClave);

  form.addEventListener("submit", (event) => {
    const emailOk = validarEmail();
    const claveOk = validarClave();

    if (!emailOk || !claveOk) {
      event.preventDefault();
    }
  });
});