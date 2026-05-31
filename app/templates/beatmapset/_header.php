<?php

use Heiakim\Model\Beatmap;

/**
 * @var Beatmap $Beatmap
 * @var Beatmap\Set $Set
 */

/**
 * Include main page navigator.
 */
include PAGE_NAVIGATOR;

?>

<div class="set__header" scroll-manipulated>
  <div class=sh__inr>

    <div class=status fl gap=smol>
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

    <div class="sh__cover">
      <pictrue>
        <?php $Beatmap->cover(big_cover: true); ?>
      </pictrue>
    </div>

    <div class="sh__content">
      <div fl align-items=center gap>
        <div class="play_button">
          <div class=pb__inr data-action="beatmaps:set,play" data-id="<?= $Set->id; ?>" clickable>
            <p play>
              <i class=mi size=wide>play_arrow</i>
            </p>
            <p pause>
              <i class=mi size=wide>pause</i>
            </p>
          </div>

          <div class=length></div>
        </div>
        <div style=flex:1; flex-truncate posrel>
          <p text bold class=title trimt><?= $Beatmap->stripped_title(); ?></p>
          <?php include TEMPLATE . "/artist/_artists_dropdown.php"; ?>
        </div>
      </div>

      <div class=sh__actions color=dynamic>
        <?php include __DIR__ . "/_modes.php"; ?>
      </div>
    </div>
  </div>
</div>