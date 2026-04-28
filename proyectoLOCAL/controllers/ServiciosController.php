<?php
require_once __DIR__ . '/../repositories/ServiciosRepository.php';
require_once __DIR__ . '/../repositories/CitasRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../helpers/i18n.php';
require_once __DIR__ . '/../security/AdminAuth.php';

class ServiciosController
{
    public static function getAll(): void
    {
        try {
            $servicios = ServiciosRepository::getActivos();
            Response::json(traducirServicios($servicios));
        } catch (Throwable $e) {
            Response::json(['error' => t('services_load_error')], 500);
        }
    }

    public static function getAllAdmin(): void
    {
        try {
            AdminAuth::user();

            $servicios = ServiciosRepository::getAll();

            Response::json($servicios);
        } catch (Throwable $e) {
            Response::json(['error' => t('services_load_error')], 500);
        }
    }

    public static function getById($id): void
    {
        try {
            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => t('service_not_found')], 404);
            }

            Response::json(traducirServicio($servicio));
        } catch (Throwable $e) {
            Response::json(['error' => t('service_load_error')], 500);
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
                Response::json(['error' => t('required_fields_invalid')], 400);
            }

            $ok = ServiciosRepository::create(
                $nombre,
                $descripcion === '' ? null : $descripcion,
                $duracion,
                $precio
            );

            if (!$ok) {
                Response::json(['error' => t('service_create_error')], 500);
            }

            Response::json(['message' => t('service_created_success')], 201);
        } catch (Throwable $e) {
            Response::json(['error' => t('service_create_error')], 500);
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
                Response::json(['error' => t('required_fields_invalid')], 400);
            }

            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => t('service_edit_not_found')], 404);
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
                Response::json(['error' => t('service_update_error')], 500);
            }

            Response::json(['message' => t('service_updated_success')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('service_update_error')], 500);
        }
    }

    public static function delete($id): void
    {
        try {
            AdminAuth::user();

            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => t('service_deactivate_not_found')], 404);
            }

            if (ServiciosRepository::tieneCitasFuturasReservadas((int) $id)) {
                Response::json([
                    'error' => t('service_has_future_appointments')
                ], 409);
            }

            $ok = ServiciosRepository::desactivar((int) $id);

            if (!$ok) {
                Response::json(['error' => t('service_deactivate_error')], 500);
            }

            Response::json(['message' => t('service_deactivated_success')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('service_deactivate_error')], 500);
        }
    }

    public static function activar($id): void
    {
        try {
            AdminAuth::user();

            $servicio = ServiciosRepository::getById($id);

            if (!$servicio) {
                Response::json(['error' => t('service_not_found')], 404);
            }

            $ok = ServiciosRepository::activar((int) $id);

            if (!$ok) {
                Response::json(['error' => t('service_activate_error')], 500);
            }

            Response::json(['message' => t('service_activated_success')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('service_activate_error')], 500);
        }
    }
}