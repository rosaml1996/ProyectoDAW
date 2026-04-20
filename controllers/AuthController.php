<?php
require_once __DIR__ . '/../repositories/PacientesRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../security/JWT.php';
require_once __DIR__ . '/../security/Auth.php';

class AuthController
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

            $paciente = PacientesRepository::findByEmail($email);

            if (!$paciente) {
                Response::json(['error' => 'No existe ningún usuario con ese correo electrónico.'], 404);
            }

            if (!password_verify($clave, $paciente['contraseña'])) {
                Response::json(['error' => 'La contraseña no es correcta.'], 401);
            }

            $payload = [
                'id_paciente' => $paciente['id_paciente'],
                'nombre' => $paciente['nombre'],
                'email' => $paciente['email']
            ];

            $jwt = JWT::generar($payload);

            Response::json([
                'message' => 'Inicio de sesión correcto.',
                'token' => $jwt,
                'usuario' => [
                    'id_paciente' => $paciente['id_paciente'],
                    'nombre' => $paciente['nombre'],
                    'email' => $paciente['email']
                ]
            ]);
        } catch (Exception $e) {
            Response::json(['error' => 'Ha ocurrido un error interno al iniciar sesión.'], 500);
        }
    }

    public static function me(): void
    {
        try {
            $usuario = Auth::user();
            Response::json($usuario);
        } catch (Exception $e) {
            Response::json(['error' => 'No se ha podido obtener la información del usuario.'], 500);
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

        Response::json(['message' => 'Sesión cerrada correctamente.']);
    }

    public static function registro(): void
    {
        try {
            $data = Request::json();

            $nombre = trim($data['nombre'] ?? '');
            $fechaNacimiento = trim($data['fecha_nacimiento'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $clave = trim($data['clave'] ?? '');
            $repetirClave = trim($data['repetir_clave'] ?? '');

            if ($nombre === '' || $fechaNacimiento === '' || $telefono === '' || !$email || $clave === '' || $repetirClave === '') {
                Response::json(['error' => 'Debes rellenar todos los campos.'], 400);
            }

            if ($clave !== $repetirClave) {
                Response::json(['error' => 'Las contraseñas no coinciden.'], 400);
            }

            if (strlen($clave) < 4) {
                Response::json(['error' => 'La contraseña debe tener al menos 4 caracteres.'], 400);
            }

            $pacienteExistente = PacientesRepository::findByEmail($email);

            if ($pacienteExistente) {
                Response::json(['error' => 'Ya existe una cuenta con ese correo electrónico.'], 409);
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
                Response::json(['error' => 'No se pudo crear la cuenta.'], 500);
            }

            Response::json([
                'message' => 'Cuenta creada correctamente. Ya puedes iniciar sesión.'
            ], 201);

        } catch (Exception $e) {
            Response::json(['error' => 'No se pudo completar el registro.'], 500);
        }
    }

    public static function actualizarPerfil(): void
    {
        try {
            $usuario = Auth::user();
            $data = Request::json();

            $nombre = trim($data['nombre'] ?? '');
            $fechaNacimiento = trim($data['fecha_nacimiento'] ?? '');
            $telefono = trim($data['telefono'] ?? '');
            $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
            $clave = trim($data['clave'] ?? '');
            $repetirClave = trim($data['repetir_clave'] ?? '');

            if ($nombre === '' || $fechaNacimiento === '' || $telefono === '' || !$email) {
                Response::json(['error' => 'Debes completar nombre, fecha de nacimiento, teléfono y correo electrónico.'], 400);
            }

            $pacienteExistente = PacientesRepository::findByEmail($email);

            if ($pacienteExistente && $pacienteExistente['id_paciente'] != $usuario['id_paciente']) {
                Response::json(['error' => 'Ya existe otra cuenta con ese correo electrónico.'], 409);
            }

            $ok = PacientesRepository::actualizar(
                $usuario['id_paciente'],
                $nombre,
                $fechaNacimiento,
                $telefono,
                $email
            );

            if (!$ok) {
                Response::json(['error' => 'No se pudieron actualizar tus datos.'], 500);
            }

            if ($clave !== '' || $repetirClave !== '') {
                if ($clave !== $repetirClave) {
                    Response::json(['error' => 'Las nuevas contraseñas no coinciden.'], 400);
                }

                if (strlen($clave) < 4) {
                    Response::json(['error' => 'La nueva contraseña debe tener al menos 4 caracteres.'], 400);
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
                'message' => 'Tu perfil se ha actualizado correctamente.',
                'token' => $jwt,
                'usuario' => $payload
            ]);
        } catch (Exception $e) {
            Response::json(['error' => 'No se pudo actualizar tu perfil.'], 500);
        }
    }

public static function perfil(): void
{
    try {
        $usuario = Auth::user();

        $perfil = PacientesRepository::getPerfilCompleto((int)$usuario['id_paciente']);

        if (!$perfil) {
            Response::json(['error' => 'No se pudo cargar tu perfil.'], 404);
        }

        Response::json($perfil);
    } catch (Exception $e) {
        Response::json(['error' => 'No se pudo obtener tu perfil.'], 500);
    }
}
}