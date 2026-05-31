<?php

/**
 * Tell the init file that this file is the maintenance page being
 * served, so it won't redirect endlessly.
 */
define("MAINTENANCE_ENABLED", 1);

/**
 * Include the init file.
 */
require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/vendor/autoload.php";
require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/define.php";
require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/init/i18n.php";

use Heiakim\Http\CSRF;

/**
 * If there is no maintenance mode enabled and the current user is
 * not an admin, redirect people back to the home page.
 */
if (!MAINTENANCE && !in_array($CurrentUser->id, [1, 2, 3, 4, 8]))
  header("location: /home");

/**
 * Sanitizes the output for non DEV environments. I don't know if
 * this actually has some good to it, but I like the look of all
 * HTML elements clutched together inside the source code.
 */
include TEMPLATE . "/global/_sanitize_html_output.php";

?>

<!DOCTYPE html>
<html lang=de>

<head>
  <meta charset="UTF-8" />
  <link rel="canonical" href="<?= HOME_URL; ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!--- Tell IE to render webpage for edge --->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="application-name" content="<?= APP_NAME; ?>">
  <meta name="keywords" content="<?= SEO_KEYWORDS; ?>" />
  <meta name="description" content="<?= SEO_DESCRIPTION; ?>" />

  <!--- Title --->
  <title><?= __("Maintenance on") ?> heia.kim</title>

  <?php

  /**
   * All the requirements being inside the head just as
   * JavaScript files abnd self written functionalities.
   */
  include TEMPLATE . "/global/_yield-requires.php";

  ?>
</head>

<body maintenance disable-user-selection>

  <?php

  /**
   * Matomo configuration.
   */
  if (PROD)
    include_once CONFIG . "/matomo.php";

  ?>

  <style>
    .stars {
      position: fixed;
      z-index: -1;
      height: 100vh;
      width: 100vw;
    }

    app {
      position: fixed;
      top: 0;
      left: 0;
      height: 100svh;
      width: 100vw;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      padding: 2.4em;
    }

    [mid] {
      position: fixed;
      bottom: 2.4em;
      left: 50%;
      translate: -50% 0;
    }
  </style>

  <?php

  if (ANIMATIONS_ENABLED) {
    echo '<div class="stars">';
    for ($i = 0; $i < 60; $i++)
      echo '<div class="snow"></div>';
    echo '</div>';
  }

  ?>

  <app>
    <div>
      <p text smol bold ttup><?= __("We'll be back soon") ?></p>
    </div>

    <div content-width=smol fl fldircol alic jucc>
      <p text bold style=font-size:6.2em;>2 0 2 4</p>
      <div fl alic gap=smolest>
        <p text wide bold>M</p>
        <picture size=std>
          <img src="<?= IMAGE . '/logo/painted/100.webp'; ?>" />
        </picture>
        <p text wide bold>INTENANCE</p>
      </div>
    </div>

    <div fl gap=smol+ alic>
      <div fl aliend gap=smol ttup>
        <p text midler bold slight>\</p>
        <p text std bold><?= date("Y", time()); ?> &copy; <?= APP_NAME; ?></p>
        <p text midler bold slight>/</p>
      </div>
    </div>
  </app>

  <script>
    __page.current = "maintenance";
  </script>
</body>

</html>

<?php include TEMPLATE . "/global/_yield-end.php"; ?>