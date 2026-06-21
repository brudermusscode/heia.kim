<?php

use Heiakim\Model\Artist;

/**
 * @var Artist $Artist
 */

$artist_play_count = $Artist->play_count();

?>


<header artist scroll-manipulated>
  <picture cover>
    <?php $Artist->cover(true); ?>
  </picture>

  <inr>
    <p name text bold trimt><?= $Artist->name; ?></p>
    <mbutton mid has-icon="left" background=company color=company-text>
      <mi>play_circle</mi>
      <p text>
        Played <strong><?= number_format($artist_play_count); ?></strong> times
      </p>
    </mbutton>
  </inr>
</header>