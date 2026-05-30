<?php

$squad_logo ??= $Squad->logo ?? "default.jpg";
$logo =
  GDPR
  ? CLAN_DEFAULT_LOGO_URL
  : (CLAN_IMAGE_URL . "/logo-images/$squad_logo");

if (DEV)
  $logo = "/squad-profile-image.webp";

?>

<img src="<?= $logo; ?>" loading=lazy />