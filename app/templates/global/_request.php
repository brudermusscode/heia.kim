<?php

/**
 * @var array
 */
$request ??= [];

?>

<request
  <?php foreach ($request as $attr => $value) : ?>
  <?= "$attr=\"$value\"" ?>
  <?php endforeach; ?>>
</request>