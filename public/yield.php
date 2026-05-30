<?php

require_once dirname(__DIR__) . "/config/init.php";

use Bruder\Application\CurrentUser;
use Bruder\Application\Cookie;

/**
 * Sanitizes the output for non DEV environments. Looks so cool
 * when going to the source code!
 */
if (PROD)
  ob_start("sanitize_output");

?>

<!DOCTYPE html>
<html lang=en>

<?php

/**
 * Create the correct canocial for google by removing the query string.
 */
$canonical = explode("?", $_SERVER["REQUEST_URI"]);
$canonical = HOME_URL . ($canonical[0] ?? "");

?>

<head>
  <meta charset="UTF-8" />

  <link rel="canonical" href="<?= $canonical ?>" />
  <link rel="home" href="<?= HOME_URL; ?>" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf_token" content="<?= $csrf_token; ?>" />
  <meta name="google-adsense-account" content="ca-pub-8743557631395381">

  <title><?= CURRENT_PAGE_TITLE ?></title>

  <!--- Tell IE to render webpage for edge --->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="application-name" content="<?= APP_NAME; ?>">
  <meta name="keywords" content="<?= SEO_KEYWORDS; ?>" />
  <meta name="description" content="<?= SEO_DESCRIPTION; ?>" />

  <?php

  /**
   * All the requirements being inside the head just as
   * JavaScript files abnd self written functionalities.
   */
  include TEMPLATE . "/global/_yield-requires.php"; ?>
</head>

<body toggled="true" initialized="false" mobile="false" class="<?= APP->current_theme_class; ?>">
  <?php

  // TODO: noscript tag.

  /**
   * Include basic matomo script for tracking users.
   */
  if (PROD)
    include_once CONFIG . "/matomo.php"; ?>

  <page-loader visible=false loading>
    <div material-bar-loader class="linear-progress-material" style="position:absolute;top:0;left:0;width:100vw;">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
    </div>
  </page-loader>

  <animate-me></animate-me>

  <responder fl alic>
    <responder-bg></responder-bg>
    <responder-inr>
      <p message text std>All right m8 it's all done cool thanks bye</p>
      <mi close-responder>close</mi>
    </responder-inr>
  </responder>

  <?php

  /**
   * Call to join us with register options.
   */
  if (!LOGGED) : ?>
    <join-now floating-action always rounded=wide filled=dark>
      <?php include TEMPLATE . "/global/_join_now.php"; ?>
    </join-now>
  <?php endif; ?>

  <!-- include page loading extras on loadup -->
  <loading-extras></loading-extras>

  <!--- backup elements -->
  <element-backup style="visibility:hidden;height:0px;width:0px;position:fixed;z-index:-1;overflow:hidden;">
  </element-backup>

  <?php

  /**
   * The overlay being displayed when freshly starting up the page
   */
  include_once TEMPLATE . "/global/_app_init_overlay.php";

  /**
   * Where all the dynamic content change magic happens! Include
   * the current page's template. You should not add anything
   * inside the <main></main> as it will be deleted when clicking
   * on a new page.
   */
  echo <<<HTML
    <main>
      $_INCLUDE_TEMPLATE
    </main>
  HTML; ?>

  <script>
    let __body = document.body;
    let __main = document.find("main");
    let __search_icon = document.find("[search-icon]");
    let __sign_icon = document.find("[sign-up-icon]");
    let __join_now = document.find("join-now");
  </script>


  <audio fail-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/bruh.mp3">
  </audio>
  <audio logout-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/logout.MP3">
  </audio>
  <audio signup-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/nya.mp3">
  </audio>
  <audio login-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/itatakimasu.mp3">
  </audio>
  <audio bell-downtoup-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/bell_downtoup.mp3">
  </audio>
  <audio bell-uptodown-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/bell_uptodown.mp3">
  </audio>
  <audio bell-negative-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/bell_negative.mp3">
  </audio>
  <audio bell-downtoup-bright-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/bell_downtoup_bright.mp3">
  </audio>
  <audio bell-highpitch-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/bell_highpitch.mp3">
  </audio>
  <audio click-audio preload="auto">
    <source type="audio/mpeg" src="<?= SOUND; ?>/t.ogg">
  </audio>

</body>

</html>

<?php template("global/yield-end") ?>