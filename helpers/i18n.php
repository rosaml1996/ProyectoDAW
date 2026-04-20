<?php

// Inicia la sesión si todavía no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Esta función devuelve los idiomas permitidos
function availableLanguages(): array
{
    return ['es', 'en'];
}

// Esta función devuelve el idioma actual guardado en sesión
function currentLanguage(): string
{
    // Si ya hay un idioma guardado y es válido, lo devolvemos
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], availableLanguages(), true)) {
        return $_SESSION['lang'];
    }

    // Si no hay nada guardado, por defecto usamos español
    return 'es';
}

// Esta función guarda el idioma elegido en la sesión
function setLanguage(string $lang): void
{
    if (in_array($lang, availableLanguages(), true)) {
        $_SESSION['lang'] = $lang;
    }
}

// Esta función carga el archivo de traducciones del idioma actual
function translations(): array
{
    $lang = currentLanguage();

    // Monta la ruta al archivo: lang/es.php o lang/en.php
    $file = __DIR__ . '/../lang/' . $lang . '.php';

    // Si el archivo existe, lo incluye y devuelve su array
    if (file_exists($file)) {
        return include $file;
    }

    // Si por algún motivo falla, carga español
    return include __DIR__ . '/../lang/es.php';
}

// Esta función recibe una clave y devuelve su texto traducido
function t(string $key): string
{
    $texts = translations();

    // Si existe la clave, devuelve el texto
    // Si no existe, devuelve la propia clave
    return $texts[$key] ?? $key;
}