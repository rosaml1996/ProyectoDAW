<?php
require_once __DIR__ . '/../repositories/ServiciosRepository.php';
require_once __DIR__ . '/../repositories/CitasRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../security/AdminAuth.php';

class ServiciosController
{
    public static function getAll(): void
    {
        try {
            $servicios = ServiciosRepository::getActivos();
            Response::json($servicios);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudieron cargar los servicios.'], 500);
        }
    }

    public static function getAllAdmin(): void
    {
        try {
            AdminAuth::user();

            $servicios = ServiciosRepository::getAll();
            Response::json($servicios);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudieron cargar los servicios.'], 500);
        }
    }

    public static function getById($id): void
    {
        try {
            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => 'El servicio no existe.'], 404);
            }

            Response::json($servicio);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo cargar el servicio.'], 500);
        }
    }

    public static function create(): void
    {
        try {
            AdminAuth::user();

            $data = Request::json();

            $nombre = trim((string) ($data['nombre'] ?? ''));
            $descripcion = trim((string) ($data['descripcion'] ?? ''));
            $duracion = (int) ($data['duracion'] ?? 0);
            $precio = (float) ($data['precio'] ?? 0);

            if ($nombre === '' || $duracion <= 0 || $precio <= 0) {
                Response::json(['error' => 'Debes completar correctamente todos los campos obligatorios.'], 400);
            }

            $ok = ServiciosRepository::create(
                $nombre,
                $descripcion === '' ? null : $descripcion,
                $duracion,
                $precio
            );

            if (!$ok) {
                Response::json(['error' => 'No se pudo crear el servicio.'], 500);
            }

            Response::json(['message' => 'Servicio creado correctamente.'], 201);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo crear el servicio.'], 500);
        }
    }

    public static function update($id): void
    {
        try {
            AdminAuth::user();

            $data = Request::json();

            $nombre = trim((string) ($data['nombre'] ?? ''));
            $descripcion = trim((string) ($data['descripcion'] ?? ''));
            $duracion = (int) ($data['duracion'] ?? 0);
            $precio = (float) ($data['precio'] ?? 0);
            $activo = filter_var($data['activo'] ?? 1, FILTER_VALIDATE_INT);

            if ($nombre === '' || $duracion <= 0 || $precio <= 0) {
                Response::json(['error' => 'Debes completar correctamente todos los campos obligatorios.'], 400);
            }

            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => 'El servicio que intentas editar no existe.'], 404);
            }

            $ok = ServiciosRepository::update(
                (int) $id,
                $nombre,
                $descripcion === '' ? null : $descripcion,
                $duracion,
                $precio,
                $activo === 0 ? 0 : 1
            );

            if (!$ok) {
                Response::json(['error' => 'No se pudo actualizar el servicio.'], 500);
            }

            Response::json(['message' => 'Servicio actualizado correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo actualizar el servicio.'], 500);
        }
    }

    public static function delete($id): void
    {
        try {
            AdminAuth::user();

            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => 'El servicio que intentas desactivar no existe.'], 404);
            }

            if (ServiciosRepository::tieneCitasFuturasReservadas((int) $id)) {
                Response::json([
                    'error' => 'No se puede desactivar el servicio porque tiene citas futuras reservadas.'
                ], 409);
            }

            $ok = ServiciosRepository::desactivar((int) $id);

            if (!$ok) {
                Response::json(['error' => 'No se pudo desactivar el servicio.'], 500);
            }

            Response::json(['message' => 'Servicio desactivado correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo desactivar el servicio.'], 500);
        }
    }

    public static function activar($id): void
    {
        try {
            AdminAuth::user();

            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => 'El servicio no existe.'], 404);
            }

            $ok = ServiciosRepository::activar((int) $id);

            if (!$ok) {
                Response::json(['error' => 'No se pudo activar el servicio.'], 500);
            }

            Response::json(['message' => 'Servicio activado correctamente.']);
        } catch (Throwable $e) {
            Response::json(['error' => 'No se pudo activar el servicio.'], 500);
        }
    }
}