<?php
require_once __DIR__ . '/../helpers/i18n.php';

function llamarApi($metodo, $ruta, $datos = null)
{
    $url = "https://ghostwhite-mantis-792007.hostingersite.com/api/index.php/" . $ruta;

    $opciones = [
        "http" => [
            "method" => $metodo,
            "header" => "Accept: application/json\r\n",
            "ignore_errors" => true
        ]
    ];

    // Enviamos el idioma actual a la API
    $opciones["http"]["header"] .= "X-Language: " . currentLanguage() . "\r\n";

    if ($datos != null) {
        $json = json_encode($datos);
        $opciones["http"]["header"] .= "Content-Type: application/json\r\n";
        $opciones["http"]["content"] = $json;
    }

    if (isset($_COOKIE["jwt"])) {
        $opciones["http"]["header"] .= "Cookie: jwt=" . $_COOKIE["jwt"] . "\r\n";
    }

    $contexto = stream_context_create($opciones);

    $respuesta = file_get_contents($url, false, $contexto);

    if ($respuesta === false) {
        return [
            "ok" => false,
            "codigo" => 500,
            "datos" => ["error" => t("api_connection_error")]
        ];
    }

    $datosRespuesta = json_decode($respuesta, true);

    if (!is_array($datosRespuesta)) {
        $datosRespuesta = ["error" => t("api_invalid_response")];
    }

    $codigo = 200;

    if (isset($http_response_header[0])) {
        preg_match('/\s(\d{3})\s/', $http_response_header[0], $coincidencias);
        if (isset($coincidencias[1])) {
            $codigo = (int)$coincidencias[1];
        }
    }

    return [
        "ok" => ($codigo >= 200 && $codigo < 300),
        "codigo" => $codigo,
        "datos" => $datosRespuesta
    ];
}