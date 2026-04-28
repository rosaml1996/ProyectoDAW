<?php
require_once __DIR__ . '/../config/database.php';

class AdminRepository
{
    public static function findByEmail(string $email): array|false
    {
        $pdo = db();

        $sql = "SELECT id_admin, nombre, email, `contraseña`
                FROM administrador
                WHERE email = :email";

        $st = $pdo->prepare($sql);
        $st->execute([
            ':email' => $email
        ]);

        return $st->fetch();
    }
}