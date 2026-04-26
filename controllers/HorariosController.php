<?php
require_once __DIR__ . '/../repositories/HorariosRepository.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Request.php';
require_once __DIR__ . '/../helpers/i18n.php';
require_once __DIR__ . '/../security/AdminAuth.php';

class HorariosController
{
    public static function getAll(): void
    {
        try {
            AdminAuth::user();
            $horarios = HorariosRepository::getAll();
            Response::json($horarios);
        } catch (Throwable $e) {
            Response::json(['error' => t('schedules_load_error')], 500);
        }
    }

    public static function create(): void
    {
        try {
            AdminAuth::user();

            $data = Request::json();

            $dia_semana = filter_var($data['dia_semana'] ?? null, FILTER_VALIDATE_INT);
            $hora_inicio = self::normalizarHora(trim((string) ($data['hora_inicio'] ?? '')));
            $hora_fin = self::normalizarHora(trim((string) ($data['hora_fin'] ?? '')));
            $activo = filter_var($data['activo'] ?? 1, FILTER_VALIDATE_INT);

            if (!$dia_semana || $hora_inicio === '' || $hora_fin === '') {
                Response::json(['error' => t('schedule_required_fields')], 400);
            }

            if ($dia_semana < 1 || $dia_semana > 7) {
                Response::json(['error' => t('weekday_invalid')], 400);
            }

            if (!self::validarHora($hora_inicio) || !self::validarHora($hora_fin)) {
                Response::json(['error' => t('time_format_invalid')], 400);
            }

            if ($hora_inicio >= $hora_fin) {
                Response::json(['error' => t('start_time_before_end_time')], 400);
            }

            if (HorariosRepository::existeSolape($dia_semana, $hora_inicio, $hora_fin)) {
                Response::json(['error' => t('schedule_overlap_error')], 409);
            }

            $id = HorariosRepository::create($dia_semana, $hora_inicio, $hora_fin, $activo === 0 ? 0 : 1);

            if (!$id) {
                Response::json(['error' => t('schedule_save_error')], 500);
            }

            Response::json([
                'message' => t('schedule_saved_success'),
                'id_horario' => $id
            ], 201);
        } catch (Throwable $e) {
            Response::json(['error' => t('schedule_save_error')], 500);
        }
    }

    public static function update(int $id): void
    {
        try {
            AdminAuth::user();

            $horario = HorariosRepository::getById($id);

            if (!$horario) {
                Response::json(['error' => t('schedule_not_found')], 404);
            }

            $data = Request::json();

            $dia_semana = filter_var($data['dia_semana'] ?? null, FILTER_VALIDATE_INT);
            $hora_inicio = self::normalizarHora(trim((string) ($data['hora_inicio'] ?? '')));
            $hora_fin = self::normalizarHora(trim((string) ($data['hora_fin'] ?? '')));
            $activo = filter_var($data['activo'] ?? 1, FILTER_VALIDATE_INT);

            if (!$dia_semana || $hora_inicio === '' || $hora_fin === '') {
                Response::json(['error' => t('schedule_required_fields')], 400);
            }

            if ($dia_semana < 1 || $dia_semana > 7) {
                Response::json(['error' => t('weekday_invalid')], 400);
            }

            if (!self::validarHora($hora_inicio) || !self::validarHora($hora_fin)) {
                Response::json(['error' => t('time_format_invalid')], 400);
            }

            if ($hora_inicio >= $hora_fin) {
                Response::json(['error' => t('start_time_before_end_time')], 400);
            }

            if (HorariosRepository::existeSolape($dia_semana, $hora_inicio, $hora_fin, $id)) {
                Response::json(['error' => t('schedule_overlap_other_error')], 409);
            }

            $ok = HorariosRepository::update($id, $dia_semana, $hora_inicio, $hora_fin, $activo === 0 ? 0 : 1);

            if (!$ok) {
                Response::json(['error' => t('schedule_update_error')], 500);
            }

            Response::json(['message' => t('schedule_updated_success')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('schedule_update_error')], 500);
        }
    }

    public static function delete(int $id): void
    {
        try {
            AdminAuth::user();

            $horario = HorariosRepository::getById($id);

            if (!$horario) {
                Response::json(['error' => t('schedule_not_found')], 404);
            }

            $ok = HorariosRepository::delete($id);

            if (!$ok) {
                Response::json(['error' => t('schedule_delete_error')], 500);
            }

            Response::json(['message' => t('schedule_deleted_success')]);
        } catch (Throwable $e) {
            Response::json(['error' => t('schedule_delete_error')], 500);
        }
    }

    private static function validarHora(string $hora): bool
    {
        $obj = DateTime::createFromFormat('H:i:s', $hora);
        return $obj && $obj->format('H:i:s') === $hora;
    }

    private static function normalizarHora(string $hora): string
    {
        if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
            return $hora . ':00';
        }

        return $hora;
    }
}