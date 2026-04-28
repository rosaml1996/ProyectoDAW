<?php
require_once __DIR__ . "/util/Html.php";
require_once __DIR__ . "/util/api.php";
require_once __DIR__ . "/helpers/i18n.php";

use util\Html;

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"] ?? "";
    $clave = $_POST["clave"] ?? "";

    $res = llamarApi("POST", "admin/login", [
        "email" => $email,
        "clave" => $clave
    ]);

    if ($res["ok"] && isset($res["datos"]["token"])) {

        $jwt = $res["datos"]["token"];

        setcookie("jwt", $jwt, [
            "expires" => time() + 3600,
            "path" => "/",
            "httponly" => true,
            "secure" => isset($_SERVER["HTTPS"]),
            "samesite" => "Lax"
        ]);

        header("Location: admin/panel.php");
        exit;

    } else {
        $error = $res["datos"]["error"] ?? t("error_admin_login_default");
    }
}

Html::inicioHtml(t("admin_login_title"), [
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
                <img src="/ProyectoDAW/img/Logo-corto.webp" alt="<?= t('site_logo_alt') ?>">
            </a>

            <div class="auth-card">
                <div class="auth-top">
                    <h1><?= t("admin_login_title") ?></h1>
                    <p><?= t("admin_login_subtitle") ?></p>
                </div>

                <?php if ($error): ?>
                    <div class="auth-alert">
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST" autocomplete="off">
                    <input type="text" name="fakeusernameremembered" style="display:none">
                    <input type="password" name="fakepasswordremembered" style="display:none">

                    <div class="auth-field">
                        <input
                            type="email"
                            name="email"
                            placeholder="<?= t("admin_login_email_placeholder") ?>"
                            required
                            value="<?= $email ? htmlspecialchars($email) : '' ?>"
                            autocomplete="off"
                            spellcheck="false"
                        >
                    </div>

                    <div class="auth-field">
                        <input
                            type="password"
                            name="clave"
                            placeholder="<?= t("admin_login_password_placeholder") ?>"
                            required
                            autocomplete="new-password"
                            spellcheck="false"
                        >
                    </div>

                    <button class="auth-btn" type="submit">
                        <?= t("admin_login_button") ?>
                    </button>
                </form>

                <div class="auth-links">
                    <a href="index.php"><?= t("login_back_web") ?></a>
                </div>

            </div>
        </div>
    </section>
</main>

<?php Html::finHtml(); 
?>