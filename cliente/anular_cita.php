<?php
require_once __DIR__ . "/../util/Html.php";
require_once __DIR__ . "/../util/api.php";
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
    $fechaHoraCita = strtotime($fecha . " " . $hora);
    return $fechaHoraCita < time();
}

function valorOrdenableCita($cita, $campo)
{
    if ($campo === "fecha") {
        return $cita["fecha"] ?? "";
    }

    if ($campo === "hora") {
        return $cita["hora"] ?? "";
    }

    if ($campo === "duracion" || $campo === "precio") {
        return (float) ($cita[$campo] ?? 0);
    }

    return mb_strtolower(trim((string) ($cita[$campo] ?? "")));
}

function ordenarCitas(array $citas, string $campo, string $direccion): array
{
    usort($citas, function ($a, $b) use ($campo, $direccion) {
        $valorA = valorOrdenableCita($a, $campo);
        $valorB = valorOrdenableCita($b, $campo);

        if ($valorA == $valorB) {
            return 0;
        }

        if ($direccion === "desc") {
            return ($valorA < $valorB) ? 1 : -1;
        }

        return ($valorA < $valorB) ? -1 : 1;
    });

    return $citas;
}

function urlOrdenCitas(string $paramSort, string $paramDir, string $campo, string $campoActual, string $dirActual): string
{
    $params = $_GET;

    $nuevaDir = "asc";
    if ($campoActual === $campo && $dirActual === "asc") {
        $nuevaDir = "desc";
    }

    $params[$paramSort] = $campo;
    $params[$paramDir] = $nuevaDir;

    return "?" . http_build_query($params);
}

function indicadorOrdenCitas(string $campo, string $campoActual, string $dirActual): string
{
    if ($campo !== $campoActual) {
        return "↕";
    }

    return $dirActual === "asc" ? "↑" : "↓";
}

function thOrdenableCitas(string $paramSort, string $paramDir, string $campo, string $texto, string $campoActual, string $dirActual): string
{
    $url = htmlspecialchars(urlOrdenCitas($paramSort, $paramDir, $campo, $campoActual, $dirActual));
    $indicador = indicadorOrdenCitas($campo, $campoActual, $dirActual);
    $clase = $campo === $campoActual ? "table-sort-link active" : "table-sort-link";

    return '<a class="' . $clase . '" href="' . $url . '">' . htmlspecialchars($texto) . ' <span class="table-sort-indicator">' . $indicador . '</span></a>';
}

$resUsuario = llamarApi("GET", "me");

if (!$resUsuario["ok"]) {
    header("Location: /ProyectoDAW/login.php");
    exit;
}

$mensaje = "";
$tipoMensaje = "";
$citas = [];
$proximasCitas = [];
$citasPasadas = [];
$hayErrorCarga = false;

$sortProximas = $_GET["sort_proximas"] ?? "fecha";
$dirProximas = strtolower($_GET["dir_proximas"] ?? "asc") === "desc" ? "desc" : "asc";

$sortHistorial = $_GET["sort_historial"] ?? "fecha";
$dirHistorial = strtolower($_GET["dir_historial"] ?? "desc") === "asc" ? "asc" : "desc";

$camposPermitidos = ["fecha", "hora", "servicio", "duracion", "precio", "estado"];

if (!in_array($sortProximas, $camposPermitidos, true)) {
    $sortProximas = "fecha";
}

if (!in_array($sortHistorial, $camposPermitidos, true)) {
    $sortHistorial = "fecha";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idCita = $_POST["id_cita"] ?? "";

    $resAnular = llamarApi("POST", "citas/anular", [
        "id_cita" => $idCita
    ]);

    if ($resAnular["ok"]) {
        $mensaje = $resAnular["datos"]["message"] ?? t("client_appointments_cancel_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resAnular["datos"]["error"] ?? t("client_appointments_cancel_error");
        $tipoMensaje = "error";
    }
}

$resCitas = llamarApi("GET", "citas/mias");

if ($resCitas["ok"] && isset($resCitas["datos"]) && is_array($resCitas["datos"])) {
    $citas = $resCitas["datos"];

    foreach ($citas as $cita) {
        if (!is_array($cita)) {
            continue;
        }

        $estado = $cita["estado"] ?? "";

        if ($estado === "reservada" && !esCitaPasada($cita["fecha"], $cita["hora"])) {
            $proximasCitas[] = $cita;
        } else {
            $citasPasadas[] = $cita;
        }
    }

    $proximasCitas = ordenarCitas($proximasCitas, $sortProximas, $dirProximas);
    $citasPasadas = ordenarCitas($citasPasadas, $sortHistorial, $dirHistorial);
} else {
    $hayErrorCarga = true;
    $mensaje = $resCitas["datos"]["error"] ?? t("client_appointments_load_error");
    $tipoMensaje = "error";
}

Html::inicioHtml(t("client_my_appointments_page_title"), [
    "/ProyectoDAW/css/normalize.css",
    "/ProyectoDAW/css/style.css"
]);
?>

<?php
$tipoHeader = 'client';
require_once __DIR__ . '/../partials/header.php';
?>

<main class="panel-wrap">
    <section class="panel-hero">
        <div class="panel-hero__text">
            <h1><?= t("client_my_appointments_page_title") ?></h1>
            <p><?= t("client_my_appointments_page_subtitle") ?></p>
        </div>
    </section>

    <section class="panel-grid" style="display:block;">
        <p><a href="/ProyectoDAW/cliente/panel.php">← <?= t("client_back_panel") ?></a></p>

        <?php if ($mensaje != ""): ?>
            <p class="<?= $tipoMensaje === 'ok' ? 'mensaje-ok' : 'mensaje-error' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </p>
        <?php endif; ?>

        <?php if (!$hayErrorCarga): ?>

            <div class="bloque-citas">
                <h2 class="titulo-seccion-panel"><?= t("client_upcoming_appointments_title") ?></h2>

                <?php if (empty($proximasCitas)): ?>
                    <p class="sin-resultados"><?= t("client_upcoming_appointments_empty") ?></p>
                <?php else: ?>
                    <div class="panel-table-wrap">
                        <table class="panel-table">
                            <thead>
                                <tr>
                                    <th><?= thOrdenableCitas("sort_proximas", "dir_proximas", "fecha", t("client_table_date"), $sortProximas, $dirProximas) ?></th>
                                    <th><?= thOrdenableCitas("sort_proximas", "dir_proximas", "hora", t("client_table_time"), $sortProximas, $dirProximas) ?></th>
                                    <th><?= thOrdenableCitas("sort_proximas", "dir_proximas", "servicio", t("client_table_service"), $sortProximas, $dirProximas) ?></th>
                                    <th><?= thOrdenableCitas("sort_proximas", "dir_proximas", "duracion", t("client_table_duration"), $sortProximas, $dirProximas) ?></th>
                                    <th><?= thOrdenableCitas("sort_proximas", "dir_proximas", "precio", t("client_table_price"), $sortProximas, $dirProximas) ?></th>
                                    <th><?= t("client_table_action") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proximasCitas as $cita): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(formatearFecha($cita["fecha"])) ?></td>
                                        <td><?= htmlspecialchars(formatearHora($cita["hora"])) ?></td>
                                        <td><?= htmlspecialchars($cita["servicio"]) ?></td>
                                        <td><?= htmlspecialchars($cita["duracion"]) ?> <?= t("minutes_short") ?></td>
                                        <td><?= htmlspecialchars($cita["precio"]) ?> €</td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn-tabla btn-anular"
                                                onclick="abrirModalAnular(
                                                    '<?= $cita['id_cita'] ?>',
                                                    '<?= htmlspecialchars(formatearFecha($cita['fecha'])) ?>',
                                                    '<?= htmlspecialchars(formatearHora($cita['hora'])) ?>',
                                                    '<?= htmlspecialchars($cita['servicio'], ENT_QUOTES) ?>'
                                                )"
                                            >
                                                <?= t("client_cancel_button") ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bloque-citas">
                <h2 class="titulo-seccion-panel">Historial de citas</h2>

                <?php if (empty($citasPasadas)): ?>
                    <p class="sin-resultados"><?= t("client_past_appointments_empty") ?></p>
                <?php else: ?>
                    <div class="panel-table-wrap">
                        <table class="panel-table">
                            <thead>
                                <tr>
                                    <th><?= thOrdenableCitas("sort_historial", "dir_historial", "fecha", t("client_table_date"), $sortHistorial, $dirHistorial) ?></th>
                                    <th><?= thOrdenableCitas("sort_historial", "dir_historial", "hora", t("client_table_time"), $sortHistorial, $dirHistorial) ?></th>
                                    <th><?= thOrdenableCitas("sort_historial", "dir_historial", "servicio", t("client_table_service"), $sortHistorial, $dirHistorial) ?></th>
                                    <th><?= thOrdenableCitas("sort_historial", "dir_historial", "duracion", t("client_table_duration"), $sortHistorial, $dirHistorial) ?></th>
                                    <th><?= thOrdenableCitas("sort_historial", "dir_historial", "precio", t("client_table_price"), $sortHistorial, $dirHistorial) ?></th>
                                    <th><?= thOrdenableCitas("sort_historial", "dir_historial", "estado", t("client_table_status"), $sortHistorial, $dirHistorial) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citasPasadas as $cita): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(formatearFecha($cita["fecha"])) ?></td>
                                        <td><?= htmlspecialchars(formatearHora($cita["hora"])) ?></td>
                                        <td><?= htmlspecialchars($cita["servicio"]) ?></td>
                                        <td><?= htmlspecialchars($cita["duracion"]) ?> <?= t("minutes_short") ?></td>
                                        <td><?= htmlspecialchars($cita["precio"]) ?> €</td>
                                        <td><?= htmlspecialchars($cita["estado"] ?? "-") ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </section>
</main>

<div id="modalAnular" class="modal-confirmacion">
    <div class="modal-box">
        <div class="modal-top">
            <h3><?= t("client_cancel_modal_title") ?></h3>
        </div>

        <div class="modal-body">
            <p id="textoModalAnular"></p>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModalAnular()">
                <?= t("cancel") ?>
            </button>

            <form method="POST" style="margin:0;">
                <input type="hidden" name="id_cita" id="inputIdCitaAnular">
                <button type="submit" class="modal-btn modal-btn-peligro">
                    <?= t("client_confirm_cancel_button") ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="/ProyectoDAW/cliente/js/modales.js"></script>

<?php Html::finHtml(); ?>