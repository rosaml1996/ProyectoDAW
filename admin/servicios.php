<?php
require_once __DIR__ . "/../util/Html.php";
require_once __DIR__ . "/../util/api.php";
require_once __DIR__ . "/../security/JWT.php";
require_once __DIR__ . "/../helpers/i18n.php";

use util\Html;

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

// CREAR SERVICIO
if (isset($_POST["crear"])) {
    $resCrear = llamarApi("POST", "servicios", [
        "nombre" => $_POST["nombre"] ?? "",
        "duracion" => $_POST["duracion"] ?? "",
        "precio" => $_POST["precio"] ?? ""
    ]);

    if ($resCrear["ok"]) {
        $mensaje = $resCrear["datos"]["message"] ?? t("admin_services_create_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resCrear["datos"]["error"] ?? t("admin_services_create_error");
        $tipoMensaje = "error";
    }
}

// EDITAR SERVICIO
if (isset($_POST["editar"])) {
    $id = $_POST["id_servicio"] ?? "";

    $resEditar = llamarApi("PUT", "servicios/" . $id, [
        "nombre" => $_POST["nombre_editar"] ?? "",
        "duracion" => $_POST["duracion_editar"] ?? "",
        "precio" => $_POST["precio_editar"] ?? ""
    ]);

    if ($resEditar["ok"]) {
        $mensaje = $resEditar["datos"]["message"] ?? t("admin_services_edit_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resEditar["datos"]["error"] ?? t("admin_services_edit_error");
        $tipoMensaje = "error";
    }
}

// ELIMINAR SERVICIO
if (isset($_POST["eliminar"])) {
    $id = $_POST["id_servicio"] ?? "";

    $resEliminar = llamarApi("DELETE", "servicios/" . $id);

    if ($resEliminar["ok"]) {
        $mensaje = $resEliminar["datos"]["message"] ?? t("admin_services_delete_success");
        $tipoMensaje = "ok";
    } else {
        $mensaje = $resEliminar["datos"]["error"] ?? t("admin_services_delete_error");
        $tipoMensaje = "error";
    }
}

// CARGAR SERVICIOS
$resServicios = llamarApi("GET", "servicios");

if ($resServicios["ok"]) {
    $servicios = $resServicios["datos"];
} else {
    $servicios = [];
    $mensaje = $resServicios["datos"]["error"] ?? t("admin_services_load_error");
    $tipoMensaje = "error";
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
                <h2><?= t("admin_services_new_title") ?></h2>
                <p><?= t("admin_services_new_subtitle") ?></p>
            </div>

            <form class="auth-form" method="POST">
                <div class="auth-field">
                    <input type="text" name="nombre" placeholder="<?= t("admin_services_name_placeholder") ?>" required>
                </div>

                <div class="auth-field">
                    <input type="number" name="duracion" placeholder="<?= t("admin_services_duration_placeholder") ?>" min="1" required>
                </div>

                <div class="auth-field">
                    <input type="number" step="0.01" name="precio" placeholder="<?= t("admin_services_price_placeholder") ?>" min="0.01" required>
                </div>

                <button class="auth-btn" type="submit" name="crear">
                    <?= t("admin_services_add_button") ?>
                </button>
            </form>
        </div>

        <div class="admin-box">
            <div class="admin-box__top">
                <h2><?= t("admin_services_list_title") ?></h2>
                <p><?= t("admin_services_list_subtitle") ?></p>
            </div>

            <?php if (empty($servicios)): ?>
                <p class="sin-resultados"><?= t("admin_services_empty") ?></p>
            <?php else: ?>
                <div class="panel-table-wrap">
                    <table class="panel-table">
                        <thead>
                            <tr>
                                <th><?= t("admin_services_table_service") ?></th>
                                <th><?= t("admin_services_table_duration") ?></th>
                                <th><?= t("admin_services_table_price") ?></th>
                                <th><?= t("admin_services_table_actions") ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($servicios as $servicio): ?>
                                <tr>
                                    <td><?= htmlspecialchars($servicio["nombre"]) ?></td>
                                    <td><?= htmlspecialchars($servicio["duracion"]) ?> <?= t("admin_services_minutes") ?></td>
                                    <td><?= number_format((float) $servicio["precio"], 2, ",", ".") ?> €</td>
                                    <td class="acciones-tabla">
                                        <button
                                            type="button"
                                            class="btn-tabla btn-reservar"
                                            onclick="abrirModalEditarServicio(
                                                '<?= $servicio['id_servicio'] ?>',
                                                '<?= htmlspecialchars($servicio['nombre'], ENT_QUOTES) ?>',
                                                '<?= $servicio['duracion'] ?>',
                                                '<?= $servicio['precio'] ?>'
                                            )"
                                        >
                                            <?= t("admin_services_edit_button") ?>
                                        </button>

                                        <button
                                            type="button"
                                            class="btn-tabla btn-anular"
                                            onclick="abrirModalEliminarServicio(
                                                '<?= $servicio['id_servicio'] ?>',
                                                '<?= htmlspecialchars($servicio['nombre'], ENT_QUOTES) ?>'
                                            )"
                                        >
                                            <?= t("admin_services_delete_button") ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- MODAL EDITAR -->
<div id="modalEditarServicio" class="modal-confirmacion">
    <div class="modal-box">
        <div class="modal-top">
            <h3><?= t("admin_services_modal_edit_title") ?></h3>
        </div>

        <div class="modal-body">
            <form method="POST" class="auth-form" id="formEditarServicio">
                <input type="hidden" name="id_servicio" id="editar_id_servicio">

                <div class="auth-field">
                    <input
                        type="text"
                        name="nombre_editar"
                        id="editar_nombre"
                        placeholder="<?= t("admin_services_name_placeholder") ?>"
                        required
                    >
                </div>

                <div class="auth-field">
                    <input
                        type="number"
                        name="duracion_editar"
                        id="editar_duracion"
                        placeholder="<?= t("admin_services_duration_placeholder") ?>"
                        min="1"
                        required
                    >
                </div>

                <div class="auth-field">
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

<!-- MODAL ELIMINAR -->
<div id="modalEliminarServicio" class="modal-confirmacion">
    <div class="modal-box">
        <div class="modal-top">
            <h3><?= t("admin_services_modal_delete_title") ?></h3>
        </div>

        <div class="modal-body">
            <p id="textoModalEliminarServicio"></p>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-cancelar" onclick="cerrarModalEliminarServicio()">
                <?= t("cancel") ?>
            </button>

            <form method="POST" style="margin:0;">
                <input type="hidden" name="id_servicio" id="eliminar_id_servicio">
                <button type="submit" name="eliminar" class="modal-btn modal-btn-peligro">
                    <?= t("confirm_delete") ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="/ProyectoDAW/js/admin_servicios.js"></script>

<?php Html::finHtml(); ?>