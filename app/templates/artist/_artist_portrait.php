<?php

$big_cover = true;

?>

<a href="/artist/<?= $Artist->id; ?>">
  <div artist-portrait clickable>
    <div class="cover">
      <picture>
        <?php $Artist->cover(); ?>
      </picture>
    </div>

    <div class="a__info tac" fl fldircol gap=smol flex-truncate align-items=center>
      <p text bold mid trimt style=line-height:1.8em;><?= htmlspecialchars_decode($Artist->name); ?></p>
    </div>
  </div>
</a>