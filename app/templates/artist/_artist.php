<?php

use Heiakim\Model\Artist;

/**
 * @var Artist $Artist
 */

$big_cover = true;

?>

<a href="/artist/<?= $Artist->id; ?>">
  <div artist outlined rounded ovhid hoverable>
    <picture style="height:80px;">
      <?php $Artist->cover(); ?>
    </picture>

    <div p24>
      <p text bold std trimt><?= $Artist->name; ?></p>
      <div fl gap=smol alic style=opacity:.6;>
        <p text smol color=company bold>
          <?= number_format($Artist->beatmapsets->count()); ?> Sets
        </p>
        <p text smol>&middot;</p>
        <p text smol><?= number_format($Artist->play_count()); ?> <?= __("Plays") ?></p>
      </div>
    </div>
  </div>
</a>