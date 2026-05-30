<?php

$big_cover = $big_cover ?? false;
$beatmap_set_id ??=
  $Set->id
  ?? $Beatmap->set_id
  ?? $Score->beatmap->set_id
  ?? 1413680;
$cover = "https://assets.ppy.sh/beatmaps/$beatmap_set_id/covers/cover" . ($big_cover ? "@2x" : "") . ".jpg";

?>

<img src="<?= $cover; ?>" loading=lazy />