<?php

$big_cover = true;

?>

<a href="/artist/<?= $Artist->id; ?>">
  <div artist outlined>
    <picture>
      <?php $Artist->cover(); ?>
    </picture>

    <div class=a__info fl fldircol gap=smol>
      <p text bold std trimt><?= $Artist->name; ?></p>
      <div fl gap=smol alic style=opacity:.6;>
        <div fl gap=smolest alic>
          <p text smol><?= number_format($Artist->beatmapsets->count()); ?> Sets
          </p>
        </div>
        <p text smol>&middot;</p>
        <p text smol><?= number_format($Artist->play_count()); ?> <?= __("Plays") ?></p>
      </div>
    </div>
  </div>
</a>