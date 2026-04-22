<?php
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CitasController.php';
require_once __DIR__ . '/../controllers/ServiciosController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/HorariosController.php';
require_once __DIR__ . '/../controllers/BloqueosController.php';

$method = $_SERVER['REQUEST_METHOD'];

// URL completa pedida
$uri = $_SERVER['REQUEST_URI'];

// Quitamos lo que va antes de la ruta real de la API
$base = '/ProyectoDAW/api/index.php/';
$ruta = str_replace($base, '', $uri);

// Por si entra solo a index.php
if ($ruta === $_SERVER['REQUEST_URI']) {
    $base2 = '/ProyectoDAW/api/index.php';
    $ruta = str_replace($base2, '', $uri);
}

$ruta = trim($ruta, '/');

// Quitamos parámetros GET si los hubiera
$ruta = explode('?', $ruta)[0];

$partes = [];

if ($ruta !== '') {
    $partes = explode('/', $ruta);
}

// LOGIN USUARIO
if ($method === 'POST' && $ruta === 'login') {
    AuthController::login();
}

// REGISTRO USUARIO
if ($method === 'POST' && $ruta === 'registro') {
    AuthController::registro();
}

// LOGOUT
if ($method === 'POST' && $ruta === 'logout') {
    AuthController::logout();
}

// USUARIO ACTUAL
if ($method === 'GET' && $ruta === 'me') {
    AuthController::me();
}

// PERFIL
if ($method === 'POST' && $ruta === 'perfil/actualizar') {
    AuthController::actualizarPerfil();
}

if ($method === 'GET' && $ruta === 'perfil') {
    AuthController::perfil();
}

// LOGIN ADMIN
if ($method === 'POST' && $ruta === 'admin/login') {
    AdminController::login();
}

// SERVICIOS PÚBLICOS / CLIENTE
if ($method === 'GET' && $ruta === 'servicios') {
    ServiciosController::getAll();
}

if (count($partes) === 2 && $partes[0] === 'servicios' && is_numeric($partes[1]) && $method === 'GET') {
    ServiciosController::getById((int) $partes[1]);
}

// SERVICIOS ADMIN
if ($method === 'GET' && $ruta === 'admin/servicios') {
    ServiciosController::getAllAdmin();
}

if ($method === 'POST' && $ruta === 'servicios') {
    ServiciosController::create();
}

if ($method === 'PUT' && preg_match('#^servicios/(\d+)$#', $ruta, $matches)) {
    ServiciosController::update((int) $matches[1]);
}

if ($method === 'DELETE' && preg_match('#^servicios/(\d+)$#', $ruta, $matches)) {
    ServiciosController::delete((int) $matches[1]);
}

if ($method === 'POST' && preg_match('#^admin/servicios/(\d+)/activar$#', $ruta, $matches)) {
    ServiciosController::activar((int) $matches[1]);
}

// CITAS CLIENTE
if ($method === 'GET' && $ruta === 'citas/disponibles') {
    CitasController::disponibles();
}

if ($method === 'GET' && $ruta === 'citas/mias') {
    CitasController::mias();
}

if ($method === 'POST' && $ruta === 'citas/reservar') {
    CitasController::reservar();
}

if ($method === 'POST' && $ruta === 'citas/anular') {
    CitasController::anular();
}

// CITAS ADMIN
if ($method === 'GET' && $ruta === 'admin/citas') {
    CitasController::getAll();
}

if ($method === 'POST' && $ruta === 'admin/citas/cancelar') {
    CitasController::cancelarAdmin();
}

// RUTAS ANTIGUAS DE CITAS
if ($method === 'POST' && $ruta === 'citas') {
    CitasController::crear();
}

if (count($partes) === 2 && $partes[0] === 'citas' && is_numeric($partes[1]) && $method === 'GET') {
    CitasController::getById((int) $partes[1]);
}

if (count($partes) === 2 && $partes[0] === 'citas' && is_numeric($partes[1]) && $method === 'PUT') {
    CitasController::actualizar((int) $partes[1]);
}

if (count($partes) === 2 && $partes[0] === 'citas' && is_numeric($partes[1]) && $method === 'DELETE') {
    CitasController::eliminar((int) $partes[1]);
}

// HORARIOS ADMIN
if ($method === 'GET' && $ruta === 'admin/horarios') {
    HorariosController::getAll();
}

if ($method === 'POST' && $ruta === 'admin/horarios') {
    HorariosController::create();
}

if ($method === 'PUT' && preg_match('#^admin/horarios/(\d+)$#', $ruta, $matches)) {
    HorariosController::update((int) $matches[1]);
}

if ($method === 'DELETE' && preg_match('#^admin/horarios/(\d+)$#', $ruta, $matches)) {
    HorariosController::delete((int) $matches[1]);
}

// BLOQUEOS ADMIN
if ($method === 'GET' && $ruta === 'admin/bloqueos') {
    BloqueosController::getAll();
}

if ($method === 'POST' && $ruta === 'admin/bloqueos') {
    BloqueosController::create();
}

if ($method === 'PUT' && preg_match('#^admin/bloqueos/(\d+)$#', $ruta, $matches)) {
    BloqueosController::update((int) $matches[1]);
}

if ($method === 'DELETE' && preg_match('#^admin/bloqueos/(\d+)$#', $ruta, $matches)) {
    BloqueosController::delete((int) $matches[1]);
}

Response::json(['error' => 'Ruta no encontrada'], 404);