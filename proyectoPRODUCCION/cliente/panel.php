<?php
require_once __DIR__ . "/../util/Html.php";
require_once __DIR__ . "/../util/api.php";
require_once __DIR__ . "/../helpers/i18n.php";

use util\Html;

$res = llamarApi("GET", "me");

if (!$res["ok"]) {
    header("Location: /login.php");
    exit;
}

$usuario = $res["datos"];
$nombreUsuario = $usuario["nombre"] ?? $usuario["email"] ?? "Usuario";

Html::inicioHtml(t("client_panel_page_title"), [
    "/css/normalize.css",
    "/css/style.css?v=final"
]);
?>

<?php
$tipoHeader = 'client';
require_once __DIR__ . '/../partials/header.php';
?>

<main class="panel-wrap">
    <section class="panel-hero">
        <div class="panel-hero__text">
            <h1><?= sprintf(t("client_panel_greeting"), htmlspecialchars($nombreUsuario)) ?> 👋</h1>
            <p><?= t("client_panel_subtitle") ?></p>
        </div>
    </section>

    <section class="panel-grid">
        <a class="panel-card" href="/cliente/citas_disponibles.php">
            <div class="panel-card__icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="panel-card__content">
                <h3><?= t("client_panel_book_title") ?></h3>
                <p><?= t("client_panel_book_text") ?></p>
            </div>
            <div class="panel-card__arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        <a class="panel-card" href="/cliente/anular_cita.php">
            <div class="panel-card__icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="panel-card__content">
                <h3><?= t("client_panel_my_appointments_title") ?></h3>
                <p><?= t("client_panel_my_appointments_text") ?></p>
            </div>
            <div class="panel-card__arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a class="panel-card" href="/cliente/perfil.php">
            <div class="panel-card__icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="panel-card__content">
                <h3><?= t("client_panel_profile_title") ?></h3>
                <p><?= t("client_panel_profile_text") ?></p>
            </div>
            <div class="panel-card__arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        <a class="panel-card panel-card--danger" href="/logout.php">
            <div class="panel-card__icon">
                <i class="fas fa-right-from-bracket"></i>
            </div>
            <div class="panel-card__content">
                <h3><?= t("client_panel_logout_title") ?></h3>
                <p><?= t("client_panel_logout_text") ?></p>
            </div>
            <div class="panel-card__arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </section>
</main>

<?php Html::finHtml(); ?>