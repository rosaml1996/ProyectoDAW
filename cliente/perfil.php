<?php
require_once __DIR__ . "/../util/Html.php";
require_once __DIR__ . "/../util/api.php";
require_once __DIR__ . "/../helpers/i18n.php";

use util\Html;

$resUsuario = llamarApi("GET", "me");

if (!$resUsuario["ok"]) {
    header("Location: /ProyectoDAW/login.php");
    exit;
}

$mensaje = "";
$tipoMensaje = "";

$nombre = "";
$fechaNacimiento = "";
$telefono = "";
$email = "";

// Cargar perfil completo
$resPerfil = llamarApi("GET", "perfil");

if ($resPerfil["ok"]) {
    $nombre = $resPerfil["datos"]["nombre"] ?? "";
    $fechaNacimiento = $resPerfil["datos"]["fecha_nacimiento"] ?? "";
    $telefono = $resPerfil["datos"]["telefono"] ?? "";
    $email = $resPerfil["datos"]["email"] ?? "";
} else {
    $mensaje = $resPerfil["datos"]["error"] ?? t("client_profile_load_error");
    $tipoMensaje = "error";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"] ?? "";
    $fechaNacimiento = $_POST["fecha_nacimiento"] ?? "";
    $telefono = $_POST["telefono"] ?? "";
    $email = $_POST["email"] ?? "";
    $clave = $_POST["clave"] ?? "";
    $repetirClave = $_POST["repetir_clave"] ?? "";

    $resActualizar = llamarApi("POST", "perfil/actualizar", [
        "nombre" => $nombre,
        "fecha_nacimiento" => $fechaNacimiento,
        "telefono" => $telefono,
        "email" => $email,
        "clave" => $clave,
        "repetir_clave" => $repetirClave
    ]);

    if ($resActualizar["ok"]) {
        $mensaje = $resActualizar["datos"]["message"] ?? t("client_profile_update_success");
        $tipoMensaje = "ok";

        if (isset($resActualizar["datos"]["token"])) {
            setcookie("jwt", $resActualizar["datos"]["token"], 0, "/");
        }
    } else {
        $mensaje = $resActualizar["datos"]["error"] ?? t("client_profile_update_error");
        $tipoMensaje = "error";
    }
}

Html::inicioHtml(t("client_profile_page_title"), [
    "/ProyectoDAW/css/normalize.css",
    "/ProyectoDAW/css/style.css"
]);
?>

<?php
$tipoHeader = 'client';
require_once __DIR__ . '/../partials/header.php';
?>

<main class="panel-wrap">
    <section class="panel-hero">
        <div class="panel-hero__text">
            <h1><?= t("client_profile_page_title") ?></h1>
            <p><?= t("client_profile_page_subtitle") ?></p>
        </div>
    </section>

    <section class="panel-grid" style="display:block;">
        <p><a href="/ProyectoDAW/cliente/panel.php">← <?= t("client_back_panel") ?></a></p>
    </section>

    <section class="auth" style="min-height:auto; padding: 35px 0 0;">
        <div class="auth-shell">
            <div class="auth-card">

                <?php if ($mensaje != ""): ?>
                    <p class="<?= $tipoMensaje === 'ok' ? 'mensaje-ok' : 'mensaje-error' ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </p>
                <?php endif; ?>

                <form class="auth-form" method="POST" id="perfilForm" novalidate>
                    <div class="auth-field" id="nombreField">
                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            placeholder="<?= t("client_profile_name_placeholder") ?>"
                            required
                            value="<?= htmlspecialchars($nombre) ?>"
                            autocomplete="name"
                        >
                    </div>
                    <span id="errorNombre" class="input-error"></span>

                    <div class="auth-field" id="fechaNacimientoField">
                        <input
                            type="date"
                            name="fecha_nacimiento"
                            id="fecha_nacimiento"
                            required
                            value="<?= htmlspecialchars($fechaNacimiento) ?>"
                            max="<?= date('Y-m-d') ?>"
                            autocomplete="bday"
                            aria-label="<?= t("client_profile_birthdate") ?>"
                            title="<?= t("client_profile_birthdate") ?>"
                        >
                    </div>
                    <span id="errorFechaNacimiento" class="input-error"></span>

                    <div class="auth-field" id="telefonoField">
                        <input
                            type="text"
                            name="telefono"
                            id="telefono"
                            placeholder="<?= t("client_profile_phone_placeholder") ?>"
                            required
                            value="<?= htmlspecialchars($telefono) ?>"
                            autocomplete="tel"
                        >
                    </div>
                    <span id="errorTelefono" class="input-error"></span>

                    <div class="auth-field" id="emailField">
                        <input
                            type="email"
                            name="email"
                            id="email"
                            placeholder="<?= t("client_profile_email_placeholder") ?>"
                            required
                            value="<?= htmlspecialchars($email) ?>"
                            autocomplete="email"
                        >
                    </div>
                    <span id="errorEmail" class="input-error"></span>

                    <div class="auth-field">
                        <input
                            type="password"
                            name="clave"
                            placeholder="<?= t("client_profile_password_placeholder") ?>"
                            value=""
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="auth-field">
                        <input
                            type="password"
                            name="repetir_clave"
                            placeholder="<?= t("client_profile_repeat_password_placeholder") ?>"
                            value=""
                            autocomplete="new-password"
                        >
                    </div>

                    <button class="auth-btn" type="submit">
                        <?= t("save_changes") ?>
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<script src="/ProyectoDAW/cliente/js/perfil.js"></script>

<?php Html::finHtml(); ?>