<?php
require_once __DIR__ . "/../util/Html.php";
require_once __DIR__ . "/../util/api.php";
require_once __DIR__ . "/../security/JWT.php";
require_once __DIR__ . "/../helpers/i18n.php";

use util\Html;

function valorOrdenableServicio($fila, $campo, $tipo = 'texto')
{
    $valor = $fila[$campo] ?? null;

    if ($tipo === 'numero') {
        return (float) $valor;
    }

    return mb_strtolower(trim((string) $valor));
}

function ordenarServicios(array $datos, string $campo, string $direccion = 'asc', string $tipo = 'texto'): array
{
    usort($datos, function ($a, $b) use ($campo, $direccion, $tipo) {
        $valorA = valorOrdenableServicio($a, $campo, $tipo);
        $valorB = valorOrdenableServicio($b, $campo, $tipo);

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

function getOrdenServicios(string $campoPorDefecto, string $dirPorDefecto = 'asc'): array
{
    $campo = $_GET['sort'] ?? $campoPorDefecto;
    $dir = strtolower($_GET['dir'] ?? $dirPorDefecto);
    $dir = $dir === 'desc' ? 'desc' : 'asc';

    return [$campo, $dir];
}

function urlOrdenServicios(string $campo, string $campoActual, string $dirActual): string
{
    $params = $_GET;

    $nuevaDir = 'asc';
    if ($campoActual === $campo && $dirActual === 'asc') {
        $nuevaDir = 'desc';
    }

    $params['sort'] = $campo;
    $params['dir'] = $nuevaDir;

    return '?' . http_build_query($params);
}

function indicadorOrdenServicios(string $campo, string $campoActual, string $dirActual): string
{
    if ($campo !== $campoActual) {
        return '↕';
    }

    return $dirActual === 'asc' ? '↑' : '↓';
}

function thOrdenableServicios(string $campo, string $texto, string $campoActual, string $dirActual): string
{
    $url = htmlspecialchars(urlOrdenServicios($campo, $campoActual, $dirActual));
    $indicador = indicadorOrdenServicios($campo, $campoActual, $dirActual);
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

if (isset($_POST["crear"])) {
    $resCrear = llamarApi("POST", "servicios", [
        "nombre" => $_POST["nombre"] ?? "",
        "descripcion" => $_POST["descripcion"] ?? "",
        "duracion" => $_POST["duracion"] ?? "",
        "precio" => $_POST["precio"] ?? ""
    ]);

    if ($resCrear["ok"]) {
        $mensaje = t("admin_services_create_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = t("admin_services_create_error");
        $tipoMensaje = "error";
    }
}

if (isset($_POST["editar"])) {
    $id = $_POST["id_servicio"] ?? "";

    $resEditar = llamarApi("PUT", "servicios/" . $id, [
        "nombre" => $_POST["nombre_editar"] ?? "",
        "descripcion" => $_POST["descripcion_editar"] ?? "",
        "duracion" => $_POST["duracion_editar"] ?? "",
        "precio" => $_POST["precio_editar"] ?? ""
    ]);

    if ($resEditar["ok"]) {
        $mensaje = t("admin_services_edit_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = t("admin_services_edit_error");
        $tipoMensaje = "error";
    }
}

if (isset($_POST["desactivar"])) {
    $id = $_POST["id_servicio"] ?? "";

    $resDesactivar = llamarApi("DELETE", "servicios/" . $id);

    if ($resDesactivar["ok"]) {
        $mensaje = t("admin_services_deactivate_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = t("admin_services_deactivate_error");
        $tipoMensaje = "error";
    }
}

if (isset($_POST["activar"])) {
    $id = $_POST["id_servicio"] ?? "";

    $resActivar = llamarApi("POST", "admin/servicios/" . $id . "/activar");

    if ($resActivar["ok"]) {
        $mensaje = t("admin_services_activate_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = t("admin_services_activate_error");
        $tipoMensaje = "error";
    }
}

$resServicios = llamarApi("GET", "admin/servicios");

if ($resServicios["ok"]) {
    $servicios = $resServicios["datos"];
} else {
    $servicios = [];
    $mensaje = t("admin_services_load_error");
    $tipoMensaje = "error";
}

[$sortCampo, $sortDir] = getOrdenServicios('nombre', 'asc');

$camposOrdenables = [
    'nombre' => 'texto',
    'descripcion' => 'texto',
    'duracion' => 'numero',
    'precio' => 'numero',
    'activo' => 'numero'
];

if (isset($camposOrdenables[$sortCampo])) {
    $servicios = ordenarServicios($servicios, $sortCampo, $sortDir, $camposOrdenables[$sortCampo]);
}

Html::inicioHtml(t("admin_services_page_title"), [
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
            <h1><?= t("admin_services_page_title") ?></h1>
            <p><?= t("admin_services_page_subtitle") ?></p>
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

    <section class="admin-services-layout admin-services-layout--stack">
        <section class="admin-box admin-services-form-box">
            <div class="admin-box__top">
                <h2><?= t("admin_services_new_title") ?></h2>
                <p><?= t("admin_services_new_subtitle") ?></p>
            </div>

            <form class="auth-form" method="POST" id="formCrearServicio" novalidate>
                <div class="auth-field" id="field_nombre">
                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        placeholder="<?= t("admin_services_name_placeholder") ?>"
                        required
                    >
                </div>
                <span id="error_nombre" class="input-error"></span>

                <div class="auth-field auth-field--textarea" id="field_descripcion">
                    <textarea
                        name="descripcion"
                        id="descripcion"
                        placeholder="<?= t("admin_services_description_placeholder") ?>"
                        rows="4"
                        required
                    ></textarea>
                </div>
                <span id="error_descripcion" class="input-error"></span>

                <div class="auth-field" id="field_duracion">
                    <input
                        type="number"
                        name="duracion"
                        id="duracion"
                        placeholder="<?= t("admin_services_duration_placeholder") ?>"
                        min="1"
                        required
                    >
                </div>
                <span id="error_duracion" class="input-error"></span>

                <div class="auth-field" id="field_precio">
                    <input
                        type="number"
                        step="0.01"
                        name="precio"
                        id="precio"
                        placeholder="<?= t("admin_services_price_placeholder") ?>"
                        min="0.01"
                        required
                    >
                </div>
                <span id="error_precio" class="input-error"></span>

                <button class="auth-btn" type="submit" name="crear">
                    <?= t("admin_services_add_button") ?>
                </button>
            </form>
        </section>

        <section class="admin-box admin-services-table-box">
            <div class="admin-box__top">
                <h2><?= t("admin_services_list_title") ?></h2>
                <p><?= t("admin_services_list_subtitle") ?></p>
            </div>

            <?php if (empty($servicios)): ?>
                <p class="sin-resultados"><?= t("admin_services_empty") ?></p>
            <?php else: ?>
                <div class="panel-table-wrap admin-services-table-wrap">
                    <table class="panel-table admin-services-table">
                        <thead>
                            <tr>
                                <th><?= thOrdenableServicios('nombre', t("admin_services_table_service"), $sortCampo, $sortDir) ?></th>
                                <th><?= thOrdenableServicios('descripcion', t("admin_services_table_description"), $sortCampo, $sortDir) ?></th>
                                <th><?= thOrdenableServicios('duracion', t("admin_services_table_duration"), $sortCampo, $sortDir) ?></th>
                                <th><?= thOrdenableServicios('precio', t("admin_services_table_price"), $sortCampo, $sortDir) ?></th>
                                <th><?= thOrdenableServicios('activo', t("admin_services_table_status"), $sortCampo, $sortDir) ?></th>
                                <th><?= t("admin_services_table_actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $servicio): ?>
                                <?php $servicioTraducido = traducirServicio($servicio); ?>

                                <tr>
                                    <td class="admin-services-col-name">
                                        <?= htmlspecialchars($servicioTraducido["nombre"]) ?>
                                    </td>
                                    <td class="admin-services-col-description">
                                        <?= nl2br(htmlspecialchars($servicioTraducido["descripcion"] ?? "")) ?>
                                    </td>
                                    <td class="admin-services-col-duration">
                                        <?= htmlspecialchars($servicio["duracion"]) ?> <?= t("admin_services_minutes") ?>
                                    </td>
                                    <td class="admin-services-col-price">
                                        <?= number_format((float) $servicio["precio"], 2, ",", ".") ?> €
                                    </td>
                                    <td class="admin-services-col-status">
                                        <?= ((int) ($servicio["activo"] ?? 1) === 1) ? t("admin_services_status_active") : t("admin_services_status_inactive") ?>
                                    </td>
                                    <td class="admin-services-actions-cell">
                                        <div class="acciones-tabla admin-services-actions">
                                            <button
                                                type="button"
                                                class="btn-tabla btn-reservar btn-editar-servicio"
                                                data-id-servicio="<?= htmlspecialchars((string) $servicio['id_servicio']) ?>"
                                                data-nombre="<?= htmlspecialchars((string) $servicio['nombre']) ?>"
                                                data-descripcion="<?= htmlspecialchars((string) ($servicio['descripcion'] ?? '')) ?>"
                                                data-duracion="<?= htmlspecialchars((string) $servicio['duracion']) ?>"
                                                data-precio="<?= htmlspecialchars((string) $servicio['precio']) ?>"
                                            >
                                                <?= t("admin_services_edit_button") ?>
                                            </button>

                                            <?php if ((int) ($servicio["activo"] ?? 1) === 1): ?>
                                                <button
                                                    type="button"
                                                    class="btn-tabla btn-anular btn-desactivar-servicio"
                                                    data-id-servicio="<?= htmlspecialchars((string) $servicio['id_servicio']) ?>"
                                                    data-nombre="<?= htmlspecialchars((string) $servicioTraducido['nombre']) ?>"
                                                >
                                                    <?= t("admin_services_deactivate_button") ?>
                                                </button>
                                            <?php else: ?>
                                                <form method="POST" style="margin:0;">
                                                    <input type="hidden" name="id_servicio" value="<?= htmlspecialchars((string) $servicio["id_servicio"]) ?>">
                                                    <button type="submit" name="activar" class="btn-tabla btn-reservar">
                                                        <?= t("admin_services_activate_button") ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

<div id="modalEditarServicio" class="modal-confirmacion">
    <div class="modal-box">
        <div class="modal-top">
            <h3><?= t("admin_services_modal_edit_title") ?></h3>
        </div>

        <div class="modal-body">
            <form method="POST" class="auth-form" id="formEditarServicio" novalidate>
                <input type="hidden" name="id_servicio" id="editar_id_servicio">

                <div class="auth-field" id="field_editar_nombre">
                    <input
                        type="text"
                        name="nombre_editar"
                        id="editar_nombre"
                        placeholder="<?= t("admin_services_name_placeholder") ?>"
                        required
                    >
                </div>
                <span id="error_editar_nombre" class="input-error"></span>

                <div class="auth-field auth-field--textarea" id="field_editar_descripcion">
                    <textarea
                        name="descripcion_editar"
                        id="editar_descripcion"
                        placeholder="<?= t("admin_services_description_placeholder") ?>"
                        rows="4"
                        required
                    ></textarea>
                </div>
                <span id="error_editar_descripcion" class="input-error"></span>

                <div class="auth-field" id="field_editar_duracion">
                    <input
                        type="number"
                        name="duracion_editar"
                        id="editar_duracion"
                        placeholder="<?= t("admin_services_duration_placeholder") ?>"
                        min="1"
                        required
                    >
                </div>
                <span id="error_editar_duracion" class="input-error"></span>

                <div class="auth-field" id="field_editar_precio">
                    <input
                        type="number"
                        step="0.01"
                        name="precio_editar"
                        id="editar_precio"
                        placeholder="<?= t("admin_services_price_placeholder") ?>"
                        min="0.01"
                        required
                    >
                </div>
                <span id="error_editar_precio" class="input-error"></span>
            </form>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModalEditarServicio()">
                <?= t("cancel") ?>
            </button>
            <button type="submit" form="formEditarServicio" name="editar" class="modal-btn modal-btn-aceptar">
                <?= t("save_changes") ?>
            </button>
        </div>
    </div>
</div>

<div id="modalDesactivarServicio" class="modal-confirmacion">
    <div class="modal-box">
        <div class="modal-top">
            <h3><?= t("admin_services_modal_deactivate_title") ?></h3>
        </div>

        <div class="modal-body">
            <p id="textoModalDesactivarServicio"></p>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModalDesactivarServicio()">
                <?= t("cancel") ?>
            </button>

            <form method="POST" style="margin:0;">
                <input type="hidden" name="id_servicio" id="desactivar_id_servicio">
                <button type="submit" name="desactivar" class="modal-btn modal-btn-peligro">
                    <?= t("admin_services_deactivate_button") ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    window.serviciosTextos = {
        deactivateConfirm: <?= json_encode(t("admin_services_deactivate_confirm")) ?>,
        requiredField: <?= json_encode(t("validation_required_field")) ?>,
        positiveInteger: <?= json_encode(t("validation_positive_integer")) ?>,
        positivePrice: <?= json_encode(t("validation_positive_price")) ?>
    };
</script>
<script src="/ProyectoDAW/admin/js/servicios.js"></script>

<?php Html::finHtml(); ?>