<?php
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/../helpers/Response.php';

class Auth
{
    public static function user(): array
    {
        $jwt = $_COOKIE['jwt'] ?? null;

        if (!$jwt) {
            Response::json(['error' => 'Debes iniciar sesión para acceder a esta página.'], 401);
        }

        $payload = JWT::verificar($jwt);

        if (!$payload) {
            Response::json(['error' => 'Tu sesión no es válida o ha caducado. Vuelve a iniciar sesión.'], 401);
        }

        return $payload;
    }
}