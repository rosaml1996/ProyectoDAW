<?php
require_once __DIR__ . "/util/Html.php";
require_once __DIR__ . "/util/api.php";
require_once __DIR__ . "/helpers/i18n.php";

use util\Html;

$error = "";
$mensaje = "";

$nombre = "";
$fechaNacimiento = "";
$telefono = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $fechaNacimiento = trim($_POST["fecha_nacimiento"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $clave = $_POST["clave"] ?? "";
    $repetirClave = $_POST["repetir_clave"] ?? "";

    $res = llamarApi("POST", "registro", [
        "nombre" => $nombre,
        "fecha_nacimiento" => $fechaNacimiento,
        "telefono" => $telefono,
        "email" => $email,
        "clave" => $clave,
        "repetir_clave" => $repetirClave
    ]);

    if ($res["ok"]) {
        $mensaje = $res["datos"]["message"] ?? t("success_register_default");
        $error = "";

        // Vaciar campos tras registro correcto
        $nombre = "";
        $fechaNacimiento = "";
        $telefono = "";
        $email = "";
    } else {
        $error = $res["datos"]["error"] ?? t("error_register_default");
        $mensaje = "";
    }
}

Html::inicioHtml(t("register_title"), [
    "/css/normalize.css",
    "/css/style.css?v=final"
]);
?>

<?php
$tipoHeader = 'auth';
require_once __DIR__ . '/partials/header.php';
?>

<main>
    <section class="auth">
        <div class="auth-shell">

            <a href="index.php" class="auth-brand">
                <img src="/img/Logo-corto.webp" alt="<?= t('site_logo_alt') ?>">
            </a>

            <div class="auth-card">
                <div class="auth-top">
                    <h1><?= t("register_title") ?></h1>
                    <p><?= t("register_subtitle") ?></p>
                </div>

                <?php if ($error): ?>
                    <div class="auth-alert">
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($mensaje): ?>
                    <p class="mensaje-ok"><?= htmlspecialchars($mensaje) ?></p>
                <?php endif; ?>

                <form class="auth-form" method="POST" id="registerForm" novalidate>
                    <div class="auth-input-group">
                        <div class="auth-field">
                            <input
                                type="text"
                                name="nombre"
                                placeholder="<?= t("register_name_placeholder") ?>"
                                value="<?= $nombre ? htmlspecialchars($nombre) : '' ?>"
                                autocomplete="name"
                            >
                        </div>
                        <div class="input-error" data-error-for="nombre"></div>
                    </div>

                    <div class="auth-input-group">
                        <div class="auth-inline-row">
                            <div class="auth-field auth-field--static">
                                <span><?= t("register_birthdate") ?></span>
                            </div>

                            <div class="auth-field">
                                <input
                                    type="date"
                                    name="fecha_nacimiento"
                                    value="<?= $fechaNacimiento ? htmlspecialchars($fechaNacimiento) : '' ?>"
                                    autocomplete="bday"
                                    aria-label="<?= t("register_birthdate") ?>"
                                    title="<?= t("register_birthdate") ?>"
                                    max="<?= date('Y-m-d') ?>"
                                >
                            </div>
                        </div>
                        <div class="input-error" data-error-for="fecha_nacimiento"></div>
                    </div>

                    <div class="auth-input-group">
                        <div class="auth-field">
                            <input
                                type="tel"
                                name="telefono"
                                placeholder="<?= t("register_phone_placeholder") ?>"
                                value="<?= $telefono ? htmlspecialchars($telefono) : '' ?>"
                                autocomplete="tel"
                                inputmode="numeric"
                                maxlength="9"
                            >
                        </div>
                        <div class="input-error" data-error-for="telefono"></div>
                    </div>

                    <div class="auth-input-group">
                        <div class="auth-field">
                            <input
                                type="email"
                                name="email"
                                placeholder="<?= t("register_email_placeholder") ?>"
                                value="<?= $email ? htmlspecialchars($email) : '' ?>"
                                autocomplete="email"
                            >
                        </div>
                        <div class="input-error" data-error-for="email"></div>
                    </div>

                    <div class="auth-input-group">
                        <div class="auth-field">
                            <input
                                type="password"
                                name="clave"
                                placeholder="<?= t("register_password_placeholder") ?>"
                                value=""
                                autocomplete="new-password"
                            >
                        </div>
                        <div class="input-error" data-error-for="clave"></div>
                    </div>

                    <div class="auth-input-group">
                        <div class="auth-field">
                            <input
                                type="password"
                                name="repetir_clave"
                                placeholder="<?= t("register_repeat_password_placeholder") ?>"
                                value=""
                                autocomplete="new-password"
                            >
                        </div>
                        <div class="input-error" data-error-for="repetir_clave"></div>
                    </div>

                    <button class="auth-btn" type="submit">
                        <?= t("register_button") ?>
                    </button>
                </form>

                <div class="auth-links">
                    <a href="login.php"><?= t("register_login_link") ?></a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
window.registroTextos = {
    requiredField: <?= json_encode(t("validation_required_field")) ?>,
    shortName: <?= json_encode(t("register_short_name")) ?>,
    invalidDate: <?= json_encode(t("register_invalid_date")) ?>,
    futureDate: <?= json_encode(t("register_future_date")) ?>,
    phoneOnlyNumbers: <?= json_encode(t("register_phone_only_numbers")) ?>,
    invalidPhone: <?= json_encode(t("register_invalid_phone")) ?>,
    invalidEmail: <?= json_encode(t("login_email_invalid")) ?>,
    shortPassword: <?= json_encode(t("password_min_length")) ?>,
    passwordsDontMatch: <?= json_encode(t("passwords_not_match")) ?>
};
</script>

<script src="/js/registro.js?v=2" defer></script>

<?php Html::finHtml(); ?>