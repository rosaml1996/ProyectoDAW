<?php
require_once __DIR__ . '/../repositories/AdminRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../helpers/i18n.php';
require_once __DIR__ . '/../security/JWT.php';

class AdminController
{
    public static function login(): void
    {
        try {
            $data = Request::json();

            $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $clave = trim($data['clave'] ?? '');

            if (!$email) {
                Response::json(['error' => t('login_email_invalid')], 400);
            }

            if ($clave === '') {
                Response::json(['error' => t('login_password_required')], 400);
            }

            $admin = AdminRepository::findByEmail($email);

            if (!$admin) {
                Response::json(['error' => t('login_admin_not_found')], 404);
            }

            if (!password_verify($clave, $admin['contraseña'])) {
                Response::json(['error' => t('login_password_incorrect')], 401);
            }

            $payload = [
                'id_admin' => $admin['id_admin'],
                'nombre' => $admin['nombre'],
                'email' => $admin['email'],
                'rol' => 'admin'
            ];

            $jwt = JWT::generar($payload);

            Response::json([
                'message' => t('login_success'),
                'token' => $jwt,
                'admin' => $payload
            ]);
        } catch (Exception $e) {
            Response::json(['error' => t('admin_login_error')], 500);
        }
    }
}