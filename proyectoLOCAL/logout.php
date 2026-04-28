<?php
setcookie("jwt", "", [
    "expires" => time() - 3600,
    "path" => "/",
    "httponly" => true,
    "secure" => isset($_SERVER["HTTPS"]),
    "samesite" => "Lax"
]);

header("Location: /ProyectoDAW/index.php");
exit;