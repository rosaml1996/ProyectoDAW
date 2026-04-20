<?php
namespace util;

require_once __DIR__ . '/../helpers/i18n.php';

class Html {

    public static function inicioHtml(string $titulo = "Sin título", array $estilos = []) {
        $lang = htmlspecialchars(currentLanguage(), ENT_QUOTES, 'UTF-8');
        ?>
        <!DOCTYPE html>
        <html lang="<?= $lang ?>">
        <head>
            <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></title>
            <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <meta charset="utf-8"/>
            <?php
            foreach ($estilos as $hoja) {
                echo "<link type='text/css' rel='stylesheet' href='$hoja'>";
            }
            ?>
        </head>
        <body>
        <?php
    }

    public static function finHtml() {
        echo <<<FIN
        </body>
        </html>
        FIN;
    }

    public static function mostrarError(\Exception $e): void {
        echo <<<ERROR
        <h3>Error de la aplicación</h3>
        <table>
          <tbody>
            <tr><th>Código de error</th><td>{$e->getCode()}</td></tr>
            <tr><th>Mensaje de error</th><td>{$e->getMessage()}</td></tr>
            <tr><th>Archivo</th><td>{$e->getFile()}</td></tr>
            <tr><th>Línea</th><td>{$e->getLine()}</td></tr>
          </tbody>
        </table>
        ERROR;
    }
}
?>