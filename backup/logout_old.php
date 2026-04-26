<?php
setcookie("jwt", "", time() - 3600, "/");
header("Location: /ProyectoDAW/index.php");
exit;