<?php
require_once __DIR__ . '/helpers/i18n.php';

header('Content-Type: application/json; charset=utf-8');

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Recoger idioma enviado
$lang = $_POST['lang'] ?? '';

// Validar idioma
if (!in_array($lang, availableLanguages(), true)) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'message' => 'Idioma no válido'
    ]);
    exit;
}

// Guardar idioma en sesión
setLanguage($lang);

// Respuesta OK
echo json_encode([
    'ok' => true,
    'lang' => currentLanguage()
]);