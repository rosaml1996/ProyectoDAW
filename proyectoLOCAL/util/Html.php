<?php
namespace util;

require_once __DIR__ . '/../helpers/i18n.php';

class Html
{
    public static function inicioHtml(string $titulo = "Sin título", array $estilos = [])
    {
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
                echo "<link type='text/css' rel='stylesheet' href='" . htmlspecialchars($hoja, ENT_QUOTES, 'UTF-8') . "'>";
            }
            ?>
        </head>
        <body>
        <?php
    }

    public static function finHtml()
    {
        echo <<<FIN
        </body>
        </html>
        FIN;
    }

    public static function mostrarError(\Exception $e): void
    {
        ?>
        <h3><?= htmlspecialchars(t("app_error_title")) ?></h3>
        <table>
            <tbody>
                <tr>
                    <th><?= htmlspecialchars(t("app_error_code")) ?></th>
                    <td><?= htmlspecialchars((string) $e->getCode()) ?></td>
                </tr>
                <tr>
                    <th><?= htmlspecialchars(t("app_error_message")) ?></th>
                    <td><?= htmlspecialchars($e->getMessage()) ?></td>
                </tr>
                <tr>
                    <th><?= htmlspecialchars(t("app_error_file")) ?></th>
                    <td><?= htmlspecialchars($e->getFile()) ?></td>
                </tr>
                <tr>
                    <th><?= htmlspecialchars(t("app_error_line")) ?></th>
                    <td><?= htmlspecialchars((string) $e->getLine()) ?></td>
                </tr>
            </tbody>
        </table>
        <?php
    }
}