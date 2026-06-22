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
    <div top fl gap=smol>
      <div class=status_option beatmap-state="<?= $Beatmap->status; ?>">
        <p text std bold ttup><?= htmlspecialchars($Beatmap->status()); ?></p>
      </div>
      <?php if ($Beatmap->is_tv_size()) { ?>
        <div class=status_option background=dynamic rounded=std has-tooltip=bottom>
          <div fl align-items=center gap=smol>
            <p>
              <i class="mi" size=smol+ bold>history_toggle_off</i>
            </p>
            <p text std bold ttup><?= __("TV Size") ?></p>
          </div>
          <div ttooltip>
            <p text std bold><?= __("Short version of a longer song") ?></p>
          </div>
        </div>
      <?php } ?>
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