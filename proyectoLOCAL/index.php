<?php
require_once __DIR__ . '/helpers/i18n.php';
?>
<!DOCTYPE html>
<html lang="<?= currentLanguage() ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description"
    content="<?= t('index_meta_description') ?>">
  <title><?= t('index_meta_title') ?></title>

  <meta name="msvalidate.01" content="D152E90F1D6A93E4631BA75637F484C2" />
  <link rel="canonical" href="https://fisioterapiapablovega.com/" />

  <meta property="og:title" content="<?= t('index_og_title') ?>" />
  <meta property="og:description"
    content="<?= t('index_og_description') ?>" />
  <meta property="og:image" content="https://fisioterapiapablovega.com/img/Logo-corto.webp" />
  <meta property="og:url" content="https://fisioterapiapablovega.com" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
    rel="stylesheet">

  <link rel="apple-touch-icon" sizes="57x57" href="favicons/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="favicons/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="favicons/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="favicons/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="favicons/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="favicons/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="favicons/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="favicons/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="favicons/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="favicons/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicons/favicon-16x16.png">
  <link rel="manifest" href="favicons/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="favicons/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
  $tipoHeader = 'public';
  require_once __DIR__ . '/partials/header.php';
  ?>

  <main>
    <div class="sobre-mi-fondo">
      <section id="sobre-mi">
        <h1 class="subrayado">
          <i class="fa-solid fa-user"></i>
          <span><?= t('index_about_title') ?></span>
        </h1>
        <div class="subsobre-mi">
          <p class="subrayado"><?= t('index_about_text') ?></p>
        </div>
      </section>
    </div>

    <div class="servicios-fondo">
      <section id="servicios">
        <h2 class="subrayado">
          <i class="fas fa-hand-holding-medical"></i>
          <span><?= t('index_services_title') ?></span>
        </h2>
        <div class="subservicios">

          <article class="card">
            <div class="container-img-card">
              <img src="img/Terapias-manuales-avanzadas.webp" alt="<?= t('service_advanced_manual_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_advanced_manual_title') ?></h3>
              <p class="descripcion"><?= t('service_advanced_manual_desc') ?></p>

              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_advanced_manual_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Electroterapia-y-neuromodulación.webp" alt="<?= t('service_electro_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_electro_title') ?></h3>
              <p class="descripcion"><?= t('service_electro_desc') ?></p>
              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_electro_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Puncion-seca.webp" alt="<?= t('service_dry_needling_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_dry_needling_title') ?></h3>
              <p class="descripcion"><?= t('service_dry_needling_desc') ?></p>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Magnetoterapia.webp" alt="<?= t('service_magnetotherapy_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_magnetotherapy_title') ?></h3>
              <p class="descripcion"><?= t('service_magnetotherapy_desc') ?></p>
              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_magnetotherapy_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Vendaje-funcional-y-kinesiología.webp" alt="<?= t('service_taping_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_taping_title') ?></h3>
              <p class="descripcion"><?= t('service_taping_desc') ?></p>
              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_taping_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Fisioterapia-deportiva.webp" alt="<?= t('service_sports_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_sports_title') ?></h3>
              <p class="descripcion"><?= t('service_sports_desc') ?></p>
              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_sports_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Fisioterapia-geriátrica.webp" alt="<?= t('service_geriatric_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_geriatric_title') ?></h3>
              <p class="descripcion"><?= t('service_geriatric_desc') ?></p>
              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_geriatric_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Fisioterapia-neurologica.webp" alt="<?= t('service_neuro_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_neuro_title') ?></h3>
              <p class="descripcion"><?= t('service_neuro_desc') ?></p>
              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_neuro_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Rehabilitación-postoperatoria.webp" alt="<?= t('service_postop_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_postop_title') ?></h3>
              <p class="descripcion"><?= t('service_postop_desc') ?></p>
              <details>
                <summary><i><?= t('more_info') ?></i></summary>
                <p class="descripcion"><?= t('service_postop_more') ?></p>
              </details>
            </div>
          </article>

          <article class="card">
            <div class="container-img-card">
              <img src="img/Ecografía-neuromusculoesquelética.webp" alt="<?= t('service_ultrasound_title') ?>">
            </div>
            <div class="container-h3-p-card subrayado-oscuro">
              <h3><?= t('service_ultrasound_title') ?></h3>
              <p class="descripcion"><?= t('service_ultrasound_desc') ?></p>
            </div>
          </article>

        </div>
      </section>
    </div>

    <div class="contacto-fondo">
      <section id="contacto">
        <h2 class="subrayado">
          <i class="fas fa-envelope"></i>
          <span><?= t('index_contact_title') ?></span>
        </h2>
        <div class="subcontacto">
          <div class="container-contacto">
            <div class="izquierda">
              <div class="horarios">
                <h3 class="subrayado-oscuro">
                  <i class="fa-regular fa-clock horario"></i>
                  <?= t('contact_schedule_title') ?>
                </h3>
                <div class="sub-horarios">
                  <p class="subrayado"><?= t('contact_morning_hours') ?></p>
                  <p class="subrayado"><?= t('contact_afternoon_hours') ?></p>
                  <p class="subrayado" style="font-style: italic;"><?= t('contact_weekend_closed') ?></p>
                </div>

              </div>
              <div class="container-whatsapp-button">
                <button class="whatsapp-button">
                  <a href="https://wa.me/34691500130?text=<?= urlencode(t('whatsapp_message')) ?>" target="_blank"
                    rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i><span
                      class="subrayado-whatsapp-button"><?= t('contact_whatsapp_button') ?></span></a>
                </button>
              </div>
              <div class="texto">
                <p class="subrayado"><i class="fa-solid fa-phone telefono"></i><span> +34 691 50 01 30</span></p>
                <p class="subrayado"><i class="fa-solid fa-envelope email"></i><span>
                    fisioterapiapablovega@gmail.com</span></p>
                <p class="subrayado"><i class="fa-solid fa-map-marker-alt direccion"></i><span> <?= t('contact_address') ?></span></p>
              </div>
            </div>

            <div class="derecha">
              <div class="mapa">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d539.2823469375705!2d-4.7902901779741125!3d37.85736274282808!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd6d2195a409935f%3A0xb86831887b55c647!2sCrossfitMania!5e0!3m2!1ses!2ses!4v1752174589657!5m2!1ses!2ses"
                  width="515" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"></iframe>
              </div>
              <div class="paradas-bus">
                <i class="fa-solid fa-bus"></i>
                <a href="https://aucorsa.es/linea/6/" target="_blank" class="subrayado6">6</a>
                <a href="https://aucorsa.es/linea/9/" target="_blank" class="subrayado9">9</a>
                <a href="https://aucorsa.es/linea/14/" target="_blank" class="subrayado14">14</a>
                <a href="https://aucorsa.es/linea/tr/" target="_blank" class="subrayadoTR">TR</a>
              </div>
            </div>
          </div>

        </div>
      </section>
    </div>

    <section class="acceso-admin">
      <div class="acceso-admin-box">
        <h2><?= t('private_access_title') ?></h2>
        <p><?= t('private_access_text') ?></p>
        <a href="admin_login.php" class="acceso-admin-btn"><?= t('private_access_button') ?></a>
      </div>
    </section>
  </main>

  <footer>
    <div class="social-icons">
      <a href="https://wa.me/34691500130?text=<?= urlencode(t('whatsapp_footer_message')) ?>"
        target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      <a href="https://www.instagram.com/fisioterapiapablovega" target="_blank" rel="noopener noreferrer"
        aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="https://www.linkedin.com/in/fisioterapia-pablo-vega-0684b8371/" target="_blank" rel="noopener noreferrer"
        aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
    </div>

    <p class="subrayado"><?= t('footer_rights') ?></p>
  </footer>
</body>
</html>