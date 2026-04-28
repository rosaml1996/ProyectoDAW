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

// Esta función devuelve el idioma actual
function currentLanguage(): string
{
    // Si la API recibe el idioma por cabecera, lo usamos
    $headerLang = $_SERVER['HTTP_X_LANGUAGE'] ?? '';

    if (in_array($headerLang, availableLanguages(), true)) {
        return $headerLang;
    }

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

    $file = __DIR__ . '/../lang/' . $lang . '.php';

    if (file_exists($file)) {
        return include $file;
    }

    return include __DIR__ . '/../lang/es.php';
}

// Esta función recibe una clave y devuelve su texto traducido
function t(string $key): string
{
    $texts = translations();

    return $texts[$key] ?? $key;
}

// Convierte un texto de BBDD en una clave segura para traducciones
function translationKeyFromText(string $text): string
{
    $text = trim(mb_strtolower($text));

    $replacements = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'à' => 'a',
        'è' => 'e',
        'ì' => 'i',
        'ò' => 'o',
        'ù' => 'u',
        'ä' => 'a',
        'ë' => 'e',
        'ï' => 'i',
        'ö' => 'o',
        'ü' => 'u',
        'ñ' => 'n',
        'ç' => 'c'
    ];

    $text = strtr($text, $replacements);
    $text = preg_replace('/[^a-z0-9]+/', '_', $text);
    $text = trim($text, '_');

    return $text;
}

// Traduce un servicio de BBDD usando claves controladas en lang/es.php y lang/en.php
function traducirServicio(array $servicio): array
{
    $nombreOriginal = $servicio['nombre'] ?? '';
    $descripcionOriginal = $servicio['descripcion'] ?? '';

    $claveBase = 'db_service_' . translationKeyFromText($nombreOriginal);

    $claveNombre = $claveBase . '_name';
    $claveDescripcion = $claveBase . '_description';

    $nombreTraducido = t($claveNombre);
    $descripcionTraducida = t($claveDescripcion);

    $servicio['nombre'] = $nombreTraducido !== $claveNombre ? $nombreTraducido : $nombreOriginal;
    $servicio['descripcion'] = $descripcionTraducida !== $claveDescripcion ? $descripcionTraducida : $descripcionOriginal;

    return $servicio;
}

// Traduce una lista de servicios de BBDD
function traducirServicios(array $servicios): array
{
    return array_map('traducirServicio', $servicios);
}