<?php
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/../helpers/Response.php';

class AdminAuth
{
    public static function user(): array
    {
        $jwt = $_COOKIE['jwt'] ?? null;

        if (!$jwt) {
            Response::json(['error' => 'Debes iniciar sesión como administrador.'], 401);
        }

        $payload = JWT::verificar($jwt);

        if (!$payload) {
            Response::json(['error' => 'Tu sesión de administrador no es válida o ha caducado.'], 401);
        }

        if (!isset($payload['rol']) || $payload['rol'] !== 'admin') {
            Response::json(['error' => 'No tienes permisos de administrador.'], 403);
        }

        return $payload;
    }
}