<?php
require_once __DIR__ . '/../repositories/PacientesRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../helpers/i18n.php';
require_once __DIR__ . '/../security/JWT.php';
require_once __DIR__ . '/../security/Auth.php';

class AuthController
{
    public static function login(): void
    {
        try {
            $data = Request::json();

            $emailTexto = trim($data['email'] ?? '');
            $email = filter_var($emailTexto, FILTER_VALIDATE_EMAIL);
            $clave = trim($data['clave'] ?? '');

            if ($emailTexto === '') {
                Response::json(['error' => t('login_email_required')], 400);
            }

            if (!$email) {
                Response::json(['error' => t('login_email_invalid')], 400);
            }

            if ($clave === '') {
                Response::json(['error' => t('login_password_required')], 400);
            }

            $paciente = PacientesRepository::findByEmail($email);

            if (!$paciente) {
                Response::json(['error' => t('login_user_not_found')], 404);
            }

            if (!password_verify($clave, $paciente['contraseña'])) {
                Response::json(['error' => t('login_password_incorrect')], 401);
            }

            $payload = [
                'id_paciente' => $paciente['id_paciente'],
                'nombre' => $paciente['nombre'],
                'email' => $paciente['email']
            ];

            $jwt = JWT::generar($payload);

            Response::json([
                'message' => t('login_success'),
                'token' => $jwt,
                'usuario' => [
                    'id_paciente' => $paciente['id_paciente'],
                    'nombre' => $paciente['nombre'],
                    'email' => $paciente['email']
                ]
            ]);
        } catch (Exception $e) {
            Response::json(['error' => t('login_internal_error')], 500);
        }
    }

    public static function me(): void
    {
        try {
            $usuario = Auth::user();
            Response::json($usuario);
        } catch (Exception $e) {
            Response::json(['error' => t('user_info_error')], 500);
        }
    }

    public static function logout(): void
    {
        setcookie('jwt', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        Response::json(['message' => t('logout_success')]);
    }

    public static function registro(): void
    {
        try {
            $data = Request::json();

            $nombre = trim($data['nombre'] ?? '');
            $fechaNacimientoTexto = trim($data['fecha_nacimiento'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $emailTexto = trim($data['email'] ?? '');
            $email = filter_var($emailTexto, FILTER_VALIDATE_EMAIL);
            $clave = trim($data['clave'] ?? '');
            $repetirClave = trim($data['repetir_clave'] ?? '');

            if (
                $nombre === '' ||
                $fechaNacimientoTexto === '' ||
                $telefono === '' ||
                $emailTexto === '' ||
                $clave === '' ||
                $repetirClave === ''
            ) {
                Response::json(['error' => t('form_all_fields_required')], 400);
            }

            if (mb_strlen($nombre) < 2) {
                Response::json(['error' => t('name_min_length')], 400);
            }

            if (!$email) {
                Response::json(['error' => t('login_email_invalid')], 400);
            }

            if (!self::telefonoValido($telefono)) {
                Response::json(['error' => t('phone_invalid')], 400);
            }

            $fechaNacimiento = self::normalizarFechaNacimiento($fechaNacimientoTexto);

            if ($fechaNacimiento === null) {
                Response::json(['error' => t('birth_date_invalid')], 400);
            }

            if ($clave !== $repetirClave) {
                Response::json(['error' => t('passwords_not_match')], 400);
            }

            if (strlen($clave) < 4) {
                Response::json(['error' => t('password_min_length')], 400);
            }

            $pacienteExistente = PacientesRepository::findByEmail($email);

            if ($pacienteExistente) {
                Response::json(['error' => t('account_email_exists')], 409);
            }

            $hash = password_hash($clave, PASSWORD_DEFAULT);

            $id = PacientesRepository::crear(
                $nombre,
                $fechaNacimiento,
                $telefono,
                $email,
                $hash
            );

            if (!$id) {
                Response::json(['error' => t('account_create_error')], 500);
            }

            Response::json([
                'message' => t('account_created_success')
            ], 201);

        } catch (Exception $e) {
            Response::json(['error' => t('register_error')], 500);
        }
    }

    public static function actualizarPerfil(): void
    {
        try {
            $usuario = Auth::user();
            $data = Request::json();

            $nombre = trim($data['nombre'] ?? '');
            $fechaNacimientoTexto = trim($data['fecha_nacimiento'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $emailTexto = trim($data['email'] ?? '');
            $email = filter_var($emailTexto, FILTER_VALIDATE_EMAIL);
            $clave = trim($data['clave'] ?? '');
            $repetirClave = trim($data['repetir_clave'] ?? '');

            if ($nombre === '' || $fechaNacimientoTexto === '' || $telefono === '' || $emailTexto === '') {
                Response::json(['error' => t('profile_required_fields')], 400);
            }

            if (mb_strlen($nombre) < 2) {
                Response::json(['error' => t('name_min_length')], 400);
            }

            if (!$email) {
                Response::json(['error' => t('login_email_invalid')], 400);
            }

            if (!self::telefonoValido($telefono)) {
                Response::json(['error' => t('phone_invalid')], 400);
            }

            $fechaNacimiento = self::normalizarFechaNacimiento($fechaNacimientoTexto);

            if ($fechaNacimiento === null) {
                Response::json(['error' => t('birth_date_invalid')], 400);
            }

            $pacienteExistente = PacientesRepository::findByEmail($email);

            if ($pacienteExistente && $pacienteExistente['id_paciente'] != $usuario['id_paciente']) {
                Response::json(['error' => t('another_account_email_exists')], 409);
            }

            $ok = PacientesRepository::actualizar(
                $usuario['id_paciente'],
                $nombre,
                $fechaNacimiento,
                $telefono,
                $email
            );

            if (!$ok) {
                Response::json(['error' => t('profile_update_data_error')], 500);
            }

            if ($clave !== '' || $repetirClave !== '') {
                if ($clave !== $repetirClave) {
                    Response::json(['error' => t('new_passwords_not_match')], 400);
                }

                if (strlen($clave) < 4) {
                    Response::json(['error' => t('new_password_min_length')], 400);
                }

                $hash = password_hash($clave, PASSWORD_DEFAULT);
                PacientesRepository::actualizarClave($usuario['id_paciente'], $hash);
            }

            $payload = [
                'id_paciente' => $usuario['id_paciente'],
                'nombre' => $nombre,
                'email' => $email
            ];

            $jwt = JWT::generar($payload);

            Response::json([
                'message' => t('profile_updated_success'),
                'token' => $jwt,
                'usuario' => $payload
            ]);
        } catch (Exception $e) {
            Response::json(['error' => t('profile_update_error')], 500);
        }
    }

    public static function perfil(): void
    {
        try {
            $usuario = Auth::user();

            $perfil = PacientesRepository::getPerfilCompleto((int)$usuario['id_paciente']);

            if (!$perfil) {
                Response::json(['error' => t('profile_load_error')], 404);
            }

            Response::json($perfil);
        } catch (Exception $e) {
            Response::json(['error' => t('profile_get_error')], 500);
        }
    }

    private static function telefonoValido(string $telefono): bool
    {
        $telefono = trim($telefono);

        return preg_match('/^[6789][0-9]{8}$/', $telefono) === 1;
    }

    private static function normalizarFechaNacimiento(string $fecha): ?string
    {
        $fecha = trim($fecha);

        if ($fecha === '') {
            return null;
        }

        $fechaObj = null;

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha)) {
            $fechaObj = DateTime::createFromFormat('d/m/Y', $fecha);
            $errores = DateTime::getLastErrors();

            if (
                !$fechaObj ||
                $errores['warning_count'] > 0 ||
                $errores['error_count'] > 0 ||
                $fechaObj->format('d/m/Y') !== $fecha
            ) {
                return null;
            }
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
            $errores = DateTime::getLastErrors();

            if (
                !$fechaObj ||
                $errores['warning_count'] > 0 ||
                $errores['error_count'] > 0 ||
                $fechaObj->format('Y-m-d') !== $fecha
            ) {
                return null;
            }
        } else {
            return null;
        }

        $hoy = new DateTime('today');

        if ($fechaObj > $hoy) {
            return null;
        }

        return $fechaObj->format('Y-m-d');
    }
}