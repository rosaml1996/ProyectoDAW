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

$resUsuario = llamarApi("GET", "me");

if (!$resUsuario["ok"]) {
    header("Location: /ProyectoDAW/login.php");
    exit;
}

$mensaje = "";
$tipoMensaje = "";
$citas = [];
$servicios = [];

$idServicio = $_GET["id_servicio"] ?? "";
$fecha = $_GET["fecha"] ?? "";

// cargar servicios
$resServicios = llamarApi("GET", "servicios");
if ($resServicios["ok"]) {
    $servicios = array_filter($resServicios["datos"], function ($s) {
        return !isset($s["activo"]) || (int) $s["activo"] === 1;
    });
}

// reservar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resReserva = llamarApi("POST", "citas/reservar", [
        "fecha" => $_POST["fecha"] ?? "",
        "hora_inicio" => $_POST["hora_inicio"] ?? "",
        "id_servicio" => $_POST["id_servicio"] ?? ""
    ]);

    if ($resReserva["ok"]) {
        $mensaje = $resReserva["datos"]["message"] ?? t("client_available_book_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resReserva["datos"]["error"] ?? t("client_available_book_error");
        $tipoMensaje = "error";
    }

    $idServicio = $_POST["id_servicio"] ?? $idServicio;
    $fecha = $_POST["fecha"] ?? $fecha;
}

// cargar horas disponibles si ya eligió servicio y fecha
if ($idServicio !== "" && $fecha !== "") {
    $ruta = "citas/disponibles?fecha=" . urlencode($fecha) . "&id_servicio=" . urlencode($idServicio);
    $resCitas = llamarApi("GET", $ruta);

    if ($resCitas["ok"]) {
        $citas = $resCitas["datos"];
    } else {
        $mensaje = $resCitas["datos"]["error"] ?? t("client_available_empty");
        $tipoMensaje = "error";
    }
}

Html::inicioHtml(t("client_available_page_title"), [
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
            <h1><?= t("client_available_page_title") ?></h1>
            <p><?= t("client_available_page_subtitle") ?></p>
        </div>
    </section>

    <section class="panel-grid" style="display:block;">
        <p><a href="/ProyectoDAW/cliente/panel.php">← <?= t("client_back_panel") ?></a></p>

        <?php if ($mensaje != ""): ?>
            <p class="<?= $tipoMensaje === 'ok' ? 'mensaje-ok' : 'mensaje-error' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </p>
        <?php endif; ?>

        <div class="admin-box" style="margin-bottom: 22px;">
            <div class="admin-box__top">
                <h2>Selecciona servicio y día</h2>
            </div>

            <form method="GET" class="auth-form">
                <div class="auth-field">
                    <select
                        name="id_servicio"
                        id="id_servicio"
                        required
                        style="width:100%; border:none; background:transparent; outline:none; font-family:inherit;"
                    >
                        <option value="">Selecciona un servicio</option>
                        <?php foreach ($servicios as $servicio): ?>
                            <option
                                value="<?= $servicio["id_servicio"] ?>"
                                data-descripcion="<?= htmlspecialchars($servicio["descripcion"] ?? "", ENT_QUOTES) ?>"
                                <?= ($idServicio == $servicio["id_servicio"]) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($servicio["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div
                    id="descripcionServicioBox"
                    class="sin-resultados"
                    style="text-align:left; margin-top: 6px; display:none;"
                >
                    <strong>Descripción:</strong><br>
                    <span id="descripcionServicioTexto"></span>
                </div>

                <div class="auth-field">
                    <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" min="<?= date('Y-m-d') ?>" required>
                </div>

                <button class="auth-btn" type="submit">
                    Ver horas disponibles
                </button>
            </form>
        </div>

        <?php if ($idServicio !== "" && $fecha !== ""): ?>
            <?php if (!empty($citas)): ?>
                <div class="panel-table-wrap">
                    <table class="panel-table">
                        <thead>
                            <tr>
                                <th><?= t("client_table_date") ?></th>
                                <th><?= t("client_table_time") ?></th>
                                <th><?= t("client_table_service") ?></th>
                                <th><?= t("client_table_duration") ?></th>
                                <th><?= t("client_table_price") ?></th>
                                <th><?= t("client_table_action") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citas as $cita): ?>
                                <tr>
                                    <td><?= htmlspecialchars(formatearFecha($cita["fecha"])) ?></td>
                                    <td><?= htmlspecialchars(formatearHora($cita["hora_inicio"])) ?></td>
                                    <td><?= htmlspecialchars($cita["servicio"]) ?></td>
                                    <td><?= htmlspecialchars($cita["duracion"]) ?> <?= t("minutes_short") ?></td>
                                    <td><?= htmlspecialchars($cita["precio"]) ?> €</td>
                                    <td>
                                        <form method="POST" style="margin:0;">
                                            <input type="hidden" name="fecha" value="<?= htmlspecialchars($cita["fecha"]) ?>">
                                            <input type="hidden" name="hora_inicio" value="<?= htmlspecialchars($cita["hora_inicio"]) ?>">
                                            <input type="hidden" name="id_servicio" value="<?= htmlspecialchars($cita["id_servicio"]) ?>">
                                            <button type="submit" class="btn-tabla btn-reservar">
                                                <?= t("client_book_button") ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="sin-resultados">No hay horas disponibles para ese servicio en esa fecha.</p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<script src="/ProyectoDAW/cliente/js/citas_disponibles.js"></script>

<?php Html::finHtml(); ?>