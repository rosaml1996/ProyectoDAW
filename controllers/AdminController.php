<?php
require_once __DIR__ . '/../repositories/AdminRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
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
                Response::json(['error' => 'Introduce un correo electrónico válido.'], 400);
            }

            if ($clave === '') {
                Response::json(['error' => 'Debes escribir tu contraseña.'], 400);
            }

            $admin = AdminRepository::findByEmail($email);

            if (!$admin) {
                Response::json(['error' => 'No existe ningún administrador con ese correo electrónico.'], 404);
            }

            if (!password_verify($clave, $admin['contraseña'])) {
                Response::json(['error' => 'La contraseña no es correcta.'], 401);
            }

            $payload = [
                'id_admin' => $admin['id_admin'],
                'nombre' => $admin['nombre'],
                'email' => $admin['email'],
                'rol' => 'admin'
            ];

            $jwt = JWT::generar($payload);

            Response::json([
                'message' => 'Inicio de sesión correcto.',
                'token' => $jwt,
                'admin' => $payload
            ]);
        } catch (Exception $e) {
            Response::json(['error' => 'No se pudo iniciar sesión como administrador.'], 500);
        }
    }
}