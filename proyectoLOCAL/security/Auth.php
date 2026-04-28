<?php
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/i18n.php';

class Auth
{
    public static function user(): array
    {
        $jwt = $_COOKIE['jwt'] ?? null;

        if (!$jwt) {
            Response::json([
                'error' => t('auth_login_required')
            ], 401);
        }

        $payload = JWT::verificar($jwt);

        if (!$payload) {
            Response::json([
                'error' => t('auth_invalid_session')
            ], 401);
        }

        return $payload;
    }
}