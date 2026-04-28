<?php
require_once __DIR__ . "/util/Html.php";
require_once __DIR__ . "/util/api.php";
require_once __DIR__ . '/helpers/i18n.php';

use util\Html;

$error = "";
$email = "";

// Si el usuario envía el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recogemos los datos
    $email = trim($_POST["email"] ?? "");
    $clave = trim($_POST["clave"] ?? "");

    // Llamamos a la API para hacer login
    $res = llamarApi("POST", "login", [
        "email" => $email,
        "clave" => $clave
    ]);

    // Si login correcto
    if ($res["ok"] && isset($res["datos"]["token"])) {

        $jwt = $res["datos"]["token"];

        // Guardamos el token en cookie
        setcookie("jwt", $jwt, 0, "/");

        // Redirigimos al panel
        header("Location: cliente/panel.php");
        exit;

    } else {
        // Error
        $error = $res["datos"]["error"] ?? t("error_login_default");
    }
}

// Iniciamos HTML
Html::inicioHtml(t("login_title"), [
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
                    <h1><?= t("login_title") ?></h1>
                    <p><?= t("login_subtitle") ?></p>
                </div>

                <?php if ($error): ?>
                    <div class="auth-alert">
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST" id="loginForm" novalidate>
                    <div class="auth-input-group">
                        <div class="auth-field">
                            <input
                                type="email"
                                name="email"
                                placeholder="<?= t("login_email_placeholder") ?>"
                                value="<?= htmlspecialchars($email) ?>"
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
                                placeholder="<?= t("login_password_placeholder") ?>"
                                autocomplete="current-password"
                            >
                        </div>
                        <div class="input-error" data-error-for="clave"></div>
                    </div>

                    <button class="auth-btn" type="submit">
                        <?= t("login_button") ?>
                    </button>
                </form>

                <div class="auth-links">
                    <a href="index.php"><?= t("login_back_web") ?></a>
                    <br><br>
                    <a href="registro.php"><?= t("login_register") ?></a>
                </div>

            </div>
        </div>
    </section>
</main>

<script src="/ProyectoDAW/js/login.js" defer></script>

<?php Html::finHtml(); ?>