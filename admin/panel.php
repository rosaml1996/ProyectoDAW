<?php
require_once __DIR__ . "/../util/Html.php";
require_once __DIR__ . "/../security/JWT.php";
require_once __DIR__ . "/../helpers/i18n.php";

use util\Html;

$jwt = $_COOKIE['jwt'] ?? null;

if (!$jwt) {
    header("Location: /ProyectoDAW/admin_login.php");
    exit;
}

$payload = JWT::verificar($jwt);

if (!$payload || !isset($payload['rol']) || $payload['rol'] !== 'admin') {
    header("Location: /ProyectoDAW/admin_login.php");
    exit;
}

$nombreAdmin = $payload["nombre"] ?? $payload["email"] ?? "Admin";

Html::inicioHtml(t("admin_panel_title"), [
    "/ProyectoDAW/css/normalize.css",
    "/ProyectoDAW/css/style.css"
]);
?>

<?php
$tipoHeader = 'admin';
require_once __DIR__ . '/../partials/header.php';
?>

<main class="panel-wrap">
    <section class="panel-hero">
        <div class="panel-hero__text">
            <h1><?= sprintf(t('admin_hero_greeting'), htmlspecialchars($nombreAdmin)) ?></h1>
            <p><?= t('admin_hero_subtitle') ?></p>
        </div>
    </section>

    <section class="panel-grid">
        <a class="panel-card" href="/ProyectoDAW/admin/servicios.php">
            <div class="panel-card__icon">
                <i class="fas fa-hand-holding-medical"></i>
            </div>
            <div class="panel-card__content">
                <h3><?= t('admin_manage_services_title') ?></h3>
                <p><?= t('admin_manage_services_text') ?></p>
            </div>
            <div class="panel-card__arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        <a class="panel-card" href="/ProyectoDAW/admin/citas.php">
            <div class="panel-card__icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="panel-card__content">
                <h3><?= t('admin_manage_appointments_title') ?></h3>
                <p><?= t('admin_manage_appointments_text') ?></p>
            </div>
            <div class="panel-card__arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </section>
</main>

<?php Html::finHtml(); ?>