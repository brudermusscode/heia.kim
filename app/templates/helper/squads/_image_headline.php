<?php

$squad_headline ??= $Squad->headline ?? "default.jpg";
$headline =
  GDPR
  ? CLAN_DEFAULT_HEADLINE_URL
  : (CLAN_IMAGE_URL . "/headline-images/$squad_headline");

if (DEV)
  $headline = "/squad-headline-image.webp";

?>

<img src="<?= $headline; ?>" loading=lazy />