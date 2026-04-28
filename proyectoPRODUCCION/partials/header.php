<?php
require_once __DIR__ . '/../helpers/i18n.php';

/*
  $tipoHeader puede venir definido desde cada página:
  - public -> logo + menú público + idioma
  - auth   -> logo + idioma
  - admin  -> logo + menú admin + idioma
  - client -> logo + menú cliente + idioma

  Si una página no define nada, por defecto usamos 'public'
*/
$tipoHeader = $tipoHeader ?? 'public';
?>

<header>
    <div class="container-header header-row">

        <a href="/index.php" class="auth-brand">
            <img
                src="/img/Logo-corto.webp"
                alt="<?= t('site_logo_alt') ?>"
                class="logo"
            >
        </a>

        <div class="header-right">
        
            <button type="button" class="menu-toggle" id="menuToggle" aria-label="<?= t('menu') ?>">
                <i class="fas fa-bars"></i>
            </button>
        
            <?php if ($tipoHeader === 'public'): ?>
                <nav id="mainNav">
                    <a href="/index.php#sobre-mi" class="subrayado-oscuro">
                        <i class="fa-regular fa-address-card"></i><?= t('nav_about') ?>
                    </a>
        
                    <a href="/index.php#servicios" class="subrayado-oscuro">
                        <i class="fas fa-hand-holding-medical"></i><?= t('nav_services') ?>
                    </a>
        
                    <a href="/index.php#contacto" class="subrayado-oscuro">
                        <i class="fas fa-envelope"></i><?= t('nav_contact') ?>
                    </a>
        
                    <a href="/login.php" class="subrayado-oscuro">
                        <i class="fas fa-user"></i><?= t('nav_login') ?>
                    </a>
                </nav>
        
            <?php elseif ($tipoHeader === 'admin'): ?>
                <nav id="mainNav">
                    <a href="/admin/panel.php" class="subrayado-oscuro">
                        <i class="fas fa-house"></i><?= t('admin_nav_panel') ?>
                    </a>
        
                    <a href="/admin/servicios.php" class="subrayado-oscuro">
                        <i class="fas fa-hand-holding-medical"></i><?= t('admin_nav_services') ?>
                    </a>
        
                    <a href="/admin/citas.php" class="subrayado-oscuro">
                        <i class="fas fa-calendar"></i><?= t('admin_nav_appointments') ?>
                    </a>
        
                    <a href="/logout.php" class="subrayado-oscuro">
                        <i class="fas fa-right-from-bracket"></i><?= t('admin_nav_logout') ?>
                    </a>
                </nav>
        
            <?php elseif ($tipoHeader === 'client'): ?>
                <nav id="mainNav">
                    <a href="/cliente/panel.php" class="subrayado-oscuro">
                        <i class="fas fa-house"></i><?= t('client_nav_panel') ?>
                    </a>
        
                    <a href="/cliente/citas_disponibles.php" class="subrayado-oscuro">
                        <i class="fas fa-calendar-plus"></i><?= t('client_nav_book') ?>
                    </a>
        
                    <a href="/cliente/anular_cita.php" class="subrayado-oscuro">
                        <i class="fas fa-calendar-check"></i><?= t('client_nav_my_appointments') ?>
                    </a>
        
                    <a href="/cliente/perfil.php" class="subrayado-oscuro">
                        <i class="fas fa-user"></i><?= t('client_nav_profile') ?>
                    </a>
        
                    <a href="/logout.php" class="subrayado-oscuro">
                        <i class="fas fa-right-from-bracket"></i><?= t('client_nav_logout') ?>
                    </a>
                </nav>
            <?php endif; ?>
        
            <div class="language-switcher">
                <label for="langSelector" class="sr-only"><?= t('language') ?></label>
                <select id="langSelector" aria-label="<?= t('language') ?>">
                    <option value="es" <?= currentLanguage() === 'es' ? 'selected' : '' ?>>ES</option>
                    <option value="en" <?= currentLanguage() === 'en' ? 'selected' : '' ?>>EN</option>
                </select>
            </div>
        </div>

    </div>
</header>

<div id="langMessage" class="mensaje-error" style="display:none;"></div>

<script>
window.headerTextos = {
    error: <?= json_encode(t("language_change_error")) ?>
};
</script>

<script src="/partials/js/header.js?v=10" defer></script>