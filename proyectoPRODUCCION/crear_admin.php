<?php
require_once __DIR__ . '/config/database.php';

$pdo = db();

$email = "itorped@g.educaand.es";
$clave = "luisusuario"; // la contraseña que quieras

$hash = password_hash($clave, PASSWORD_DEFAULT);

$sql = "INSERT INTO administrador (email, contraseña)
        VALUES (:email, :clave)";

$st = $pdo->prepare($sql);

$st->execute([
    ':email' => $email,
    ':clave' => $hash
]);

echo "Admin creado correctamente";