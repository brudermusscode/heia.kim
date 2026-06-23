<?php

use Heiakim\Model\Artist;

/**
 * @var Artist $Artist
 */

$big_cover = true;

?>

<a href="/artist/<?= $Artist->id; ?>">
  <div artist filled rounded clickable>
    <picture style="height:140px;" rounded ovhid>
      <?php $Artist->cover(); ?>
    </picture>

    <div pinline24 pblock18>
      <p text midler bold trimt><?= $Artist->name; ?></p>
      <div fl gap=smol+ alic>
        <p text smol>
          Beatmaps &middot;
          <strong color=company>
            <?= number_format($Artist->beatmapsets->count()); ?></strong>
        </p>
        <p text smol>
          <?= __("Plays") ?> &middot; <strong color=company>
            <?= number_format($Artist->play_count()); ?></strong></p>
      </div>
    </div>
  </div>
</a>