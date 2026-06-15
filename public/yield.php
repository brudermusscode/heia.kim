<?php

require dirname(__DIR__) . "/config/init.php";

# Sanitize the output for non DEV environments. Looks cool 🙂
if (PROD) ob_start("sanitize_output"); ?>

<!DOCTYPE html>
<html lang=en>

<head>
  <link rel="canonical"
    href="<?= HOME_URL . explode("?", $_SERVER["REQUEST_URI"])[0] ?>" />
  <link rel="home" href="<?= HOME_URL; ?>" />

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="application-name" content="<?= APP_NAME; ?>">
  <meta name="keywords" content="<?= SEO_KEYWORDS; ?>" />
  <meta name="description" content="<?= SEO_DESCRIPTION; ?>" />

  <title><?= CURRENT_PAGE_TITLE ?></title>

  <?php

  # + JS, styles and other definitions.
  include TEMPLATE . "/global/_yield-requires.php"; ?>
</head>

<body toggled="true" initialized="false" mobile="false"
  class="<?= APP->current_theme_class; ?>">

  <?php

  # + Matomo in Production only.
  if (PROD) include CONFIG . "/matomo.php"; ?>

  <page-loader visible=false loading>
    <div class="linear-progress-material" in-overlay>
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
    </div>
  </page-loader>

  <ajax-response></ajax-response>

  <animate-me></animate-me>

  <responder fl alic>
    <responder-bg></responder-bg>
    <responder-inr>
      <p message text std></p>
      <mi close-responder>close</mi>
    </responder-inr>
  </responder>

  <?php

  # + Join us banner with register options.
  if (!LOGGED) : ?>
    <join-now floating-action always rounded=wide filled=dark>
      <?php include TEMPLATE . "/global/_join_now.php"; ?>
    </join-now>
  <?php endif; ?>

  <?php

  # + App startup overlay.
  include_once TEMPLATE . "/global/_app_init_overlay.php"; ?>

  <!--- Include HTML that is returned by the Router --->
  <main><?= YIELD_OUTPUT ?></main>

  <!--- Preload some audio files, can be used in JavaScript --->
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