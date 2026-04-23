<?php
require_once __DIR__ . "/../util/Html.php";
require_once __DIR__ . "/../util/api.php";
require_once __DIR__ . "/../security/JWT.php";
require_once __DIR__ . "/../helpers/i18n.php";

use util\Html;

function formatearFecha($fecha)
{
    $fechaObj = DateTime::createFromFormat("Y-m-d", $fecha);
    return $fechaObj ? $fechaObj->format("d/m/Y") : $fecha;
}

function formatearHora($hora)
{
    $horaObj = DateTime::createFromFormat("H:i:s", $hora);
    return $horaObj ? $horaObj->format("H:i") : $hora;
}

function esCitaPasada($fecha, $hora)
{
    $fechaHora = strtotime($fecha . " " . $hora);
    return $fechaHora < time();
}

function nombreDiaSemana($dia)
{
    $dias = [
        1 => "Lunes",
        2 => "Martes",
        3 => "Miércoles",
        4 => "Jueves",
        5 => "Viernes",
        6 => "Sábado",
        7 => "Domingo"
    ];

    return $dias[$dia] ?? "Día";
}

function valorOrdenable($fila, $campo, $tipo = 'texto')
{
    $valor = $fila[$campo] ?? null;

    if ($tipo === 'numero') {
        return (float) $valor;
    }

    if ($tipo === 'fecha') {
        return strtotime((string) $valor) ?: 0;
    }

    if ($tipo === 'hora') {
        return strtotime('1970-01-01 ' . (string) $valor) ?: 0;
    }

    return mb_strtolower(trim((string) $valor));
}

function ordenarArrayPorCampo(array $datos, string $campo, string $direccion = 'asc', string $tipo = 'texto'): array
{
    usort($datos, function ($a, $b) use ($campo, $direccion, $tipo) {
        $valorA = valorOrdenable($a, $campo, $tipo);
        $valorB = valorOrdenable($b, $campo, $tipo);

        if ($valorA == $valorB) {
            return 0;
        }

        if ($direccion === 'desc') {
            return ($valorA < $valorB) ? 1 : -1;
        }

        return ($valorA < $valorB) ? -1 : 1;
    });

    return $datos;
}

function getOrdenTabla(string $tabla, string $campoPorDefecto, string $dirPorDefecto = 'asc'): array
{
    $campo = $_GET[$tabla . '_sort'] ?? $campoPorDefecto;
    $dir = strtolower($_GET[$tabla . '_dir'] ?? $dirPorDefecto);
    $dir = $dir === 'desc' ? 'desc' : 'asc';

    return [$campo, $dir];
}

function urlOrdenTabla(string $tabla, string $campo, string $campoActual, string $dirActual): string
{
    $params = $_GET;

    $nuevaDir = 'asc';
    if ($campoActual === $campo && $dirActual === 'asc') {
        $nuevaDir = 'desc';
    }

    $params[$tabla . '_sort'] = $campo;
    $params[$tabla . '_dir'] = $nuevaDir;

    return '?' . http_build_query($params);
}

function indicadorOrden(string $campo, string $campoActual, string $dirActual): string
{
    if ($campo !== $campoActual) {
        return '↕';
    }

    return $dirActual === 'asc' ? '↑' : '↓';
}

function thOrdenable(string $tabla, string $campo, string $texto, string $campoActual, string $dirActual): string
{
    $url = htmlspecialchars(urlOrdenTabla($tabla, $campo, $campoActual, $dirActual));
    $indicador = indicadorOrden($campo, $campoActual, $dirActual);
    $clase = $campo === $campoActual ? 'table-sort-link active' : 'table-sort-link';

    return '<a class="' . $clase . '" href="' . $url . '">' . htmlspecialchars($texto) . ' <span class="table-sort-indicator">' . $indicador . '</span></a>';
}

$jwt = $_COOKIE['jwt'] ?? null;

if (!$jwt) {
    header("Location: /ProyectoDAW/admin_login.php");
    exit;
}

$payload = JWT::verificar($jwt);

if (!$payload || !isset($payload["rol"]) || $payload["rol"] !== "admin") {
    header("Location: /ProyectoDAW/admin_login.php");
    exit;
}

$mensaje = "";
$tipoMensaje = "";

// =====================================================
// ACCIONES
// =====================================================

if (isset($_POST["cancelar_cita"])) {
    $id = $_POST["id_cita"] ?? "";

    $resCancelar = llamarApi("POST", "admin/citas/cancelar", [
        "id_cita" => $id
    ]);

    if ($resCancelar["ok"]) {
        $mensaje = $resCancelar["datos"]["message"] ?? "Cita cancelada correctamente.";
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resCancelar["datos"]["error"] ?? "No se pudo cancelar la cita.";
        $tipoMensaje = "error";
    }
}

if (isset($_POST["crear_horario"])) {
    $resCrearHorario = llamarApi("POST", "admin/horarios", [
        "dia_semana" => $_POST["dia_semana"] ?? "",
        "hora_inicio" => $_POST["hora_inicio"] ?? "",
        "hora_fin" => $_POST["hora_fin"] ?? ""
    ]);

    if ($resCrearHorario["ok"]) {
        $mensaje = $resCrearHorario["datos"]["message"] ?? "Horario guardado correctamente.";
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resCrearHorario["datos"]["error"] ?? "No se pudo guardar el horario.";
        $tipoMensaje = "error";
    }
}

if (isset($_POST["editar_horario"])) {
    $id = $_POST["id_horario"] ?? "";

    $resEditarHorario = llamarApi("PUT", "admin/horarios/" . $id, [
        "dia_semana" => $_POST["dia_semana_editar"] ?? "",
        "hora_inicio" => $_POST["hora_inicio_editar"] ?? "",
        "hora_fin" => $_POST["hora_fin_editar"] ?? "",
        "activo" => $_POST["activo_editar"] ?? "1"
    ]);

    if ($resEditarHorario["ok"]) {
        $mensaje = $resEditarHorario["datos"]["message"] ?? "Horario actualizado correctamente.";
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resEditarHorario["datos"]["error"] ?? "No se pudo actualizar el horario.";
        $tipoMensaje = "error";
    }
}

if (isset($_POST["eliminar_horario"])) {
    $id = $_POST["id_horario"] ?? "";

    $resEliminarHorario = llamarApi("DELETE", "admin/horarios/" . $id);

    if ($resEliminarHorario["ok"]) {
        $mensaje = $resEliminarHorario["datos"]["message"] ?? "Horario eliminado correctamente.";
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resEliminarHorario["datos"]["error"] ?? "No se pudo eliminar el horario.";
        $tipoMensaje = "error";
    }
}

if (isset($_POST["crear_bloqueo"])) {
    $horaInicio = trim($_POST["hora_inicio_bloqueo"] ?? "");
    $horaFin = trim($_POST["hora_fin_bloqueo"] ?? "");

    $resCrearBloqueo = llamarApi("POST", "admin/bloqueos", [
        "fecha" => $_POST["fecha_bloqueo"] ?? "",
        "hora_inicio" => $horaInicio === "" ? null : $horaInicio,
        "hora_fin" => $horaFin === "" ? null : $horaFin,
        "motivo" => $_POST["motivo_bloqueo"] ?? ""
    ]);

    if ($resCrearBloqueo["ok"]) {
        $mensaje = $resCrearBloqueo["datos"]["message"] ?? "Bloqueo guardado correctamente.";
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resCrearBloqueo["datos"]["error"] ?? "No se pudo guardar el bloqueo.";
        $tipoMensaje = "error";
    }
}

if (isset($_POST["editar_bloqueo"])) {
    $id = $_POST["id_bloqueo"] ?? "";
    $horaInicio = trim($_POST["hora_inicio_bloqueo_editar"] ?? "");
    $horaFin = trim($_POST["hora_fin_bloqueo_editar"] ?? "");

    $resEditarBloqueo = llamarApi("PUT", "admin/bloqueos/" . $id, [
        "fecha" => $_POST["fecha_bloqueo_editar"] ?? "",
        "hora_inicio" => $horaInicio === "" ? null : $horaInicio,
        "hora_fin" => $horaFin === "" ? null : $horaFin,
        "motivo" => $_POST["motivo_bloqueo_editar"] ?? ""
    ]);

    if ($resEditarBloqueo["ok"]) {
        $mensaje = $resEditarBloqueo["datos"]["message"] ?? "Bloqueo actualizado correctamente.";
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resEditarBloqueo["datos"]["error"] ?? "No se pudo actualizar el bloqueo.";
        $tipoMensaje = "error";
    }
}

if (isset($_POST["eliminar_bloqueo"])) {
    $id = $_POST["id_bloqueo"] ?? "";

    $resEliminarBloqueo = llamarApi("DELETE", "admin/bloqueos/" . $id);

    if ($resEliminarBloqueo["ok"]) {
        $mensaje = $resEliminarBloqueo["datos"]["message"] ?? "Bloqueo eliminado correctamente.";
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resEliminarBloqueo["datos"]["error"] ?? "No se pudo eliminar el bloqueo.";
        $tipoMensaje = "error";
    }
}

// =====================================================
// CARGA DE DATOS
// =====================================================

$resCitas = llamarApi("GET", "admin/citas");
$citas = $resCitas["ok"] ? $resCitas["datos"] : [];

$resHorarios = llamarApi("GET", "admin/horarios");
$horarios = $resHorarios["ok"] ? $resHorarios["datos"] : [];

$resBloqueos = llamarApi("GET", "admin/bloqueos");
$bloqueos = $resBloqueos["ok"] ? $resBloqueos["datos"] : [];

$proximasCitas = [];
$historialCitas = [];

foreach ($citas as $cita) {
    $estado = $cita["estado"] ?? "";

    if ($estado === "reservada" && !esCitaPasada($cita["fecha"], $cita["hora"])) {
        $proximasCitas[] = $cita;
    } else {
        $historialCitas[] = $cita;
    }
}

// =====================================================
// ORDENACIÓN
// =====================================================

[$horariosSort, $horariosDir] = getOrdenTabla('horarios', 'dia_semana', 'asc');
$camposHorarios = [
    'dia_semana' => 'numero',
    'hora_inicio' => 'hora',
    'hora_fin' => 'hora',
    'activo' => 'numero'
];
if (isset($camposHorarios[$horariosSort])) {
    $horarios = ordenarArrayPorCampo($horarios, $horariosSort, $horariosDir, $camposHorarios[$horariosSort]);
}

[$bloqueosSort, $bloqueosDir] = getOrdenTabla('bloqueos', 'fecha', 'desc');
$camposBloqueos = [
    'fecha' => 'fecha',
    'hora_inicio' => 'hora',
    'hora_fin' => 'hora',
    'motivo' => 'texto'
];
if (isset($camposBloqueos[$bloqueosSort])) {
    $bloqueos = ordenarArrayPorCampo($bloqueos, $bloqueosSort, $bloqueosDir, $camposBloqueos[$bloqueosSort]);
}

[$proximasSort, $proximasDir] = getOrdenTabla('proximas', 'fecha', 'asc');
$camposProximas = [
    'fecha' => 'fecha',
    'hora' => 'hora',
    'servicio' => 'texto',
    'estado' => 'texto',
    'paciente' => 'texto'
];
if (isset($camposProximas[$proximasSort])) {
    $proximasCitas = ordenarArrayPorCampo($proximasCitas, $proximasSort, $proximasDir, $camposProximas[$proximasSort]);
}

[$historialSort, $historialDir] = getOrdenTabla('historial', 'fecha', 'desc');
$camposHistorial = [
    'fecha' => 'fecha',
    'hora' => 'hora',
    'servicio' => 'texto',
    'estado' => 'texto',
    'paciente' => 'texto'
];
if (isset($camposHistorial[$historialSort])) {
    $historialCitas = ordenarArrayPorCampo($historialCitas, $historialSort, $historialDir, $camposHistorial[$historialSort]);
}

Html::inicioHtml(t("admin_appointments_page_title"), [
    "/ProyectoDAW/css/normalize.css",
    "/ProyectoDAW/css/style.css"
]);
?>

<?php
$tipoHeader = 'admin';
require_once __DIR__ . '/../partials/header.php';
?>

<main class="panel-wrap">
    <section class="panel-hero">
        <div class="panel-hero__text">
            <h1><?= t("admin_appointments_page_title") ?></h1>
            <p>Gestiona citas reales, horario habitual y bloqueos de agenda.</p>
        </div>
    </section>

    <section class="panel-grid" style="display:block;">
        <p><a href="/ProyectoDAW/admin/panel.php">← <?= t("client_back_panel") ?></a></p>
    </section>

    <?php if ($mensaje != ""): ?>
        <section style="margin-top: 18px;">
            <p class="<?= $tipoMensaje === 'ok' ? 'mensaje-ok' : 'mensaje-error' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </p>
        </section>
    <?php endif; ?>

    <section class="admin-layout">
        <div class="admin-box">
            <div class="admin-box__top">
                <h2>Horario habitual</h2>
                <p>Añade los días y tramos en los que trabajas normalmente.</p>
            </div>

            <form class="auth-form" method="POST">
                <div class="auth-field">
                    <select
                        name="dia_semana"
                        required
                        style="width:100%; border:none; background:transparent; outline:none; font-family:inherit;"
                    >
                        <option value="">Selecciona un día</option>
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </div>

                <div class="auth-field">
                    <input type="time" name="hora_inicio" required>
                </div>

                <div class="auth-field">
                    <input type="time" name="hora_fin" required>
                </div>

                <button class="auth-btn" type="submit" name="crear_horario">
                    Guardar horario
                </button>
            </form>
        </div>

        <div class="admin-box">
            <div class="admin-box__top">
                <h2>Bloqueos de agenda</h2>
                <p>Puedes bloquear un día completo o solo una franja horaria.</p>
            </div>

            <form class="auth-form" method="POST" id="formCrearBloqueo">
                <div class="auth-field" id="crearBloqueoFechaField">
                    <input
                        type="date"
                        name="fecha_bloqueo"
                        id="fecha_bloqueo"
                        min="<?= date('Y-m-d') ?>"
                        required
                    >
                </div>
                <span id="errorCrearBloqueoFecha" class="input-error"></span>

                <div class="auth-field">
                    <input type="time" name="hora_inicio_bloqueo" id="hora_inicio_bloqueo">
                </div>

                <div class="auth-field">
                    <input type="time" name="hora_fin_bloqueo" id="hora_fin_bloqueo">
                </div>
                <span id="errorCrearBloqueoHoras" class="input-error"></span>

                <div class="auth-field" id="crearBloqueoMotivoField">
                    <input
                        type="text"
                        name="motivo_bloqueo"
                        id="motivo_bloqueo"
                        placeholder="Motivo"
                        required
                    >
                </div>
                <span id="errorCrearBloqueoMotivo" class="input-error"></span>

                <button class="auth-btn" type="submit" name="crear_bloqueo">
                    Guardar bloqueo
                </button>
            </form>

            <p style="margin-top:10px; font-size:14px;">
                Si dejas vacías las horas, se bloqueará el día completo.
            </p>
        </div>
    </section>

    <section class="admin-box" style="margin-top: 22px;">
        <div class="admin-box__top">
            <h2>Horarios configurados</h2>
        </div>

        <?php if (empty($horarios)): ?>
            <p class="sin-resultados">No hay horarios configurados.</p>
        <?php else: ?>
            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th><?= thOrdenable('horarios', 'dia_semana', 'Día', $horariosSort, $horariosDir) ?></th>
                            <th><?= thOrdenable('horarios', 'hora_inicio', 'Inicio', $horariosSort, $horariosDir) ?></th>
                            <th><?= thOrdenable('horarios', 'hora_fin', 'Fin', $horariosSort, $horariosDir) ?></th>
                            <th><?= thOrdenable('horarios', 'activo', 'Activo', $horariosSort, $horariosDir) ?></th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horarios as $horario): ?>
                            <tr>
                                <td><?= htmlspecialchars(nombreDiaSemana((int) $horario["dia_semana"])) ?></td>
                                <td><?= htmlspecialchars(formatearHora($horario["hora_inicio"])) ?></td>
                                <td><?= htmlspecialchars(formatearHora($horario["hora_fin"])) ?></td>
                                <td><?= ((int) $horario["activo"] === 1) ? "Sí" : "No" ?></td>
                                <td class="acciones-tabla">
                                    <button
                                        type="button"
                                        class="btn-tabla btn-reservar"
                                        onclick="abrirModalEditarHorario(
                                            '<?= $horario['id_horario'] ?>',
                                            '<?= $horario['dia_semana'] ?>',
                                            '<?= substr($horario['hora_inicio'], 0, 5) ?>',
                                            '<?= substr($horario['hora_fin'], 0, 5) ?>',
                                            '<?= $horario['activo'] ?>'
                                        )"
                                    >
                                        Editar
                                    </button>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_horario" value="<?= $horario["id_horario"] ?>">
                                        <button type="submit" name="eliminar_horario" class="btn-tabla btn-anular">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="admin-box" style="margin-top: 22px;">
        <div class="admin-box__top">
            <h2>Bloqueos configurados</h2>
        </div>

        <?php if (empty($bloqueos)): ?>
            <p class="sin-resultados">No hay bloqueos configurados.</p>
        <?php else: ?>
            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th><?= thOrdenable('bloqueos', 'fecha', 'Fecha', $bloqueosSort, $bloqueosDir) ?></th>
                            <th><?= thOrdenable('bloqueos', 'hora_inicio', 'Inicio', $bloqueosSort, $bloqueosDir) ?></th>
                            <th><?= thOrdenable('bloqueos', 'hora_fin', 'Fin', $bloqueosSort, $bloqueosDir) ?></th>
                            <th><?= thOrdenable('bloqueos', 'motivo', 'Motivo', $bloqueosSort, $bloqueosDir) ?></th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bloqueos as $bloqueo): ?>
                            <tr>
                                <td><?= htmlspecialchars(formatearFecha($bloqueo["fecha"])) ?></td>
                                <td><?= htmlspecialchars($bloqueo["hora_inicio"] ? formatearHora($bloqueo["hora_inicio"]) : "Día completo") ?></td>
                                <td><?= htmlspecialchars($bloqueo["hora_fin"] ? formatearHora($bloqueo["hora_fin"]) : "-") ?></td>
                                <td><?= htmlspecialchars($bloqueo["motivo"] ?? "") ?></td>
                                <td class="acciones-tabla">
                                    <button
                                        type="button"
                                        class="btn-tabla btn-reservar"
                                        onclick="abrirModalEditarBloqueo(
                                            '<?= $bloqueo['id_bloqueo'] ?>',
                                            '<?= $bloqueo['fecha'] ?>',
                                            '<?= $bloqueo['hora_inicio'] ? substr($bloqueo['hora_inicio'], 0, 5) : '' ?>',
                                            '<?= $bloqueo['hora_fin'] ? substr($bloqueo['hora_fin'], 0, 5) : '' ?>',
                                            '<?= htmlspecialchars($bloqueo['motivo'] ?? '', ENT_QUOTES) ?>'
                                        )"
                                    >
                                        Editar
                                    </button>

                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="id_bloqueo" value="<?= $bloqueo["id_bloqueo"] ?>">
                                        <button type="submit" name="eliminar_bloqueo" class="btn-tabla btn-anular">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="admin-box" style="margin-top: 22px;">
        <div class="admin-box__top">
            <h2>Próximas citas</h2>
            <p>Citas futuras reservadas por los clientes.</p>
        </div>

        <?php if (empty($proximasCitas)): ?>
            <p class="sin-resultados">No hay próximas citas.</p>
        <?php else: ?>
            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th><?= thOrdenable('proximas', 'fecha', 'Fecha', $proximasSort, $proximasDir) ?></th>
                            <th><?= thOrdenable('proximas', 'hora', 'Hora', $proximasSort, $proximasDir) ?></th>
                            <th><?= thOrdenable('proximas', 'servicio', 'Servicio', $proximasSort, $proximasDir) ?></th>
                            <th><?= thOrdenable('proximas', 'estado', 'Estado', $proximasSort, $proximasDir) ?></th>
                            <th><?= thOrdenable('proximas', 'paciente', 'Paciente', $proximasSort, $proximasDir) ?></th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proximasCitas as $cita): ?>
                            <tr>
                                <td><?= htmlspecialchars(formatearFecha($cita["fecha"])) ?></td>
                                <td><?= htmlspecialchars(formatearHora($cita["hora"])) ?></td>
                                <td><?= htmlspecialchars($cita["servicio"]) ?></td>
                                <td><?= htmlspecialchars($cita["estado"]) ?></td>
                                <td><?= htmlspecialchars($cita["paciente"] ?? "-") ?></td>
                                <td>
                                    <?php if (($cita["estado"] ?? "") === "reservada"): ?>
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="id_cita" value="<?= $cita["id_cita"] ?>">
                                            <button type="submit" name="cancelar_cita" class="btn-tabla btn-anular">
                                                Cancelar
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="admin-box" style="margin-top: 22px;">
        <div class="admin-box__top">
            <h2>Historial de citas</h2>
            <p>Citas pasadas, canceladas o ya finalizadas.</p>
        </div>

        <?php if (empty($historialCitas)): ?>
            <p class="sin-resultados">No hay historial de citas.</p>
        <?php else: ?>
            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th><?= thOrdenable('historial', 'fecha', 'Fecha', $historialSort, $historialDir) ?></th>
                            <th><?= thOrdenable('historial', 'hora', 'Hora', $historialSort, $historialDir) ?></th>
                            <th><?= thOrdenable('historial', 'servicio', 'Servicio', $historialSort, $historialDir) ?></th>
                            <th><?= thOrdenable('historial', 'estado', 'Estado', $historialSort, $historialDir) ?></th>
                            <th><?= thOrdenable('historial', 'paciente', 'Paciente', $historialSort, $historialDir) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historialCitas as $cita): ?>
                            <tr>
                                <td><?= htmlspecialchars(formatearFecha($cita["fecha"])) ?></td>
                                <td><?= htmlspecialchars(formatearHora($cita["hora"])) ?></td>
                                <td><?= htmlspecialchars($cita["servicio"]) ?></td>
                                <td><?= htmlspecialchars($cita["estado"]) ?></td>
                                <td><?= htmlspecialchars($cita["paciente"] ?? "-") ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<div id="modalEditarHorario" class="modal-confirmacion">
    <div class="modal-box">
        <div class="modal-top">
            <h3>Editar horario</h3>
        </div>

        <div class="modal-body">
            <form method="POST" class="auth-form" id="formEditarHorario">
                <input type="hidden" name="id_horario" id="editar_id_horario">

                <div class="auth-field">
                    <select
                        name="dia_semana_editar"
                        id="editar_dia_semana"
                        required
                        style="width:100%; border:none; background:transparent; outline:none; font-family:inherit;"
                    >
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </div>

                <div class="auth-field">
                    <input type="time" name="hora_inicio_editar" id="editar_hora_inicio" required>
                </div>

                <div class="auth-field">
                    <input type="time" name="hora_fin_editar" id="editar_hora_fin" required>
                </div>

                <div class="auth-field">
                    <select
                        name="activo_editar"
                        id="editar_activo"
                        required
                        style="width:100%; border:none; background:transparent; outline:none; font-family:inherit;"
                    >
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModalEditarHorario()">
                Cancelar
            </button>
            <button type="submit" form="formEditarHorario" name="editar_horario" class="modal-btn modal-btn-aceptar">
                Guardar cambios
            </button>
        </div>
    </div>
</div>

<div id="modalEditarBloqueo" class="modal-confirmacion">
    <div class="modal-box">
        <div class="modal-top">
            <h3>Editar bloqueo</h3>
        </div>

        <div class="modal-body">
            <form method="POST" class="auth-form" id="formEditarBloqueo">
                <input type="hidden" name="id_bloqueo" id="editar_id_bloqueo">

                <div class="auth-field" id="editarBloqueoFechaField">
                    <input
                        type="date"
                        name="fecha_bloqueo_editar"
                        id="editar_fecha_bloqueo"
                        min="<?= date('Y-m-d') ?>"
                        required
                    >
                </div>
                <span id="errorEditarBloqueoFecha" class="input-error"></span>

                <div class="auth-field">
                    <input type="time" name="hora_inicio_bloqueo_editar" id="editar_hora_inicio_bloqueo">
                </div>

                <div class="auth-field">
                    <input type="time" name="hora_fin_bloqueo_editar" id="editar_hora_fin_bloqueo">
                </div>
                <span id="errorEditarBloqueoHoras" class="input-error"></span>

                <div class="auth-field" id="editarBloqueoMotivoField">
                    <input
                        type="text"
                        name="motivo_bloqueo_editar"
                        id="editar_motivo_bloqueo"
                        placeholder="Motivo"
                        required
                    >
                </div>
                <span id="errorEditarBloqueoMotivo" class="input-error"></span>
            </form>

            <p style="margin-top:10px; font-size:14px;">
                Si dejas vacías las horas, se bloqueará el día completo.
            </p>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModalEditarBloqueo()">
                Cancelar
            </button>
            <button type="submit" form="formEditarBloqueo" name="editar_bloqueo" class="modal-btn modal-btn-aceptar">
                Guardar cambios
            </button>
        </div>
    </div>
</div>

<script src="/ProyectoDAW/admin/js/citas.js"></script>

<?php Html::finHtml(); ?>