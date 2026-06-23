<?php

use Heiakim\Model\Beatmap;

/**
 * @var Beatmap $Beatmap
 * @var Beatmap\Set $Set
 */

?>

<div class="set__header" scroll-manipulated>
  <picture cover>
    <?php $Beatmap->cover(big_cover: true); ?>
  </picture>

  <inr>
    <div top fl jucsb alistart gap=smol>
      <div class=status_option beatmap-state="<?= $Beatmap->status; ?>">
        <p text std bold ttup><?= htmlspecialchars($Beatmap->status()); ?></p>
      </div>
      <div fl alic gap=smoler>
        <?php if ($Beatmap->is_tv_size()) : ?>
          <div class=status_option background=dynamic rounded=std has-tooltip=bottom>
            <div fl align-items=center gap=smol>
              <mi std bold>tv_gen</mi>
              <p text std bold ttup><?= __("TV Size") ?></p>
            </div>
            <div ttooltip>
              <p text std bold><?= __("Short version of a longer song") ?></p>
            </div>
          </div>
        <?php endif;

        if ($Artists->count() > 1) : ?>
          <div class=status_option background=dynamic rounded=std has-tooltip=bottom>
            <div fl align-items=center gap=smol>
              <mi std bold>diversity_3</mi>
              <p text std bold ttup><?= __("Feature") ?></p>
            </div>
            <div ttooltip>
              <p text std bold><?= __("Feature between two or more artists") ?></p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div bottom w100 fl alic>
      <play-track>
        <play-button data-action="beatmap:set:play" data-id="<?= $Set->id; ?>"
          paused>
          <mi size=wide></mi>
        </play-button>

        <track-duration background=special rounded>&nbsp;</track-duration>
      </play-track>

      <div title flone flex-truncate posrel>
        <p text bold trimt><?= $Beatmap->stripped_title(); ?></p>
        <?php include TEMPLATE . "/artist/_artists_dropdown.php"; ?>
      </div>
    </div>
  </inr>
</div>