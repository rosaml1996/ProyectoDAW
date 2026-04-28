<?php

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=127.0.0.1;dbname=u883799062_proyectofisio;charset=utf8mb4";
        $usuario = "u883799062_adminfisio";
        $password = ";wI~iu*aE^J5";

        $pdo = new PDO($dsn, $usuario, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    return $pdo;
}