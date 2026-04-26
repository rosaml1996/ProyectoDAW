<?php
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/i18n.php';

class AdminAuth
{
    public static function user(): array
    {
        $jwt = $_COOKIE['jwt'] ?? null;

        if (!$jwt) {
            Response::json(['error' => t('admin_auth_login_required')], 401);
        }

        $payload = JWT::verificar($jwt);

        if (!$payload) {
            Response::json(['error' => t('admin_auth_invalid_session')], 401);
        }

        if (!isset($payload['rol']) || $payload['rol'] !== 'admin') {
            Response::json(['error' => t('admin_auth_forbidden')], 403);
        }

        return $payload;
    }
}