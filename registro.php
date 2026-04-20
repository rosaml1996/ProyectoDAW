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

    $nombre = $_POST["nombre"] ?? "";
    $fechaNacimiento = $_POST["fecha_nacimiento"] ?? "";
    $telefono = $_POST["telefono"] ?? "";
    $email = $_POST["email"] ?? "";
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
    "/ProyectoDAW/css/normalize.css",
    "/ProyectoDAW/css/style.css"
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
                <img src="/ProyectoDAW/img/Logo-corto.webp" alt="Logo de Fisioterapia Pablo Vega">
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

                <form class="auth-form" method="POST">
                    <div class="auth-field">
                        <input
                            type="text"
                            name="nombre"
                            placeholder="<?= t("register_name_placeholder") ?>"
                            required
                            value="<?= $nombre ? htmlspecialchars($nombre) : '' ?>"
                            autocomplete="name"
                        >
                    </div>

                    <div class="auth-field">
                        <label for="fecha_nacimiento"><?= t("register_birthdate") ?></label>
                        <input
                            type="date"
                            name="fecha_nacimiento"
                            placeholder="<?= t("register_birthdate") ?>"
                            required
                            value="<?= $fechaNacimiento ? htmlspecialchars($fechaNacimiento) : '' ?>"
                            autocomplete="bday"
                            aria-label="<?= t("register_birthdate") ?>"
                            title="<?= t("register_birthdate") ?>"
                        >
                    </div>

                    <div class="auth-field">
                        <input
                            type="text"
                            name="telefono"
                            placeholder="<?= t("register_phone_placeholder") ?>"
                            required
                            value="<?= $telefono ? htmlspecialchars($telefono) : '' ?>"
                            autocomplete="tel"
                        >
                    </div>

                    <div class="auth-field">
                        <input
                            type="email"
                            name="email"
                            placeholder="<?= t("register_email_placeholder") ?>"
                            required
                            value="<?= $email ? htmlspecialchars($email) : '' ?>"
                            autocomplete="email"
                        >
                    </div>

                    <div class="auth-field">
                        <input
                            type="password"
                            name="clave"
                            placeholder="<?= t("register_password_placeholder") ?>"
                            required
                            value=""
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="auth-field">
                        <input
                            type="password"
                            name="repetir_clave"
                            placeholder="<?= t("register_repeat_password_placeholder") ?>"
                            required
                            value=""
                            autocomplete="new-password"
                        >
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

<?php Html::finHtml(); ?>