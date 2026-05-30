<?php

/**
 * @var User $CurrentUser
 * @var Beatmap\Set $Set
 * @var Beatmap $Beatmap
 */

?>

<div class="s__modes">
  <?php

  foreach ($Beatmaps as $SetBeatmap) {
    /**
     * Icon to display
     */
    $icon = match ($mode) {
      "taiko" => 'taiko',
      "ctb" => 'ctb',
      "mania" => 'mania',
      default => 'vanilla',
    };

    /**
     * @var bool
     */
    $is_active = $map_id === $SetBeatmap->id;

  ?>

    <a class=version sub ripple-effect href='<?= "/beatmap-set/$Set->id/$SetBeatmap->id/$mode/$current_mod"; ?>'
      <?= !$is_active ? "has-tooltip=bottom no-trans-delay" : "active=true"; ?>>
      <div class="s__modes_option" fl alic>
        <div <?= "beatmap-difficulty-color=" . $SetBeatmap->difficulty(); ?>>
          <p class="option_text"><i class="osu-icon osu-<?= $icon; ?>"></i></p>
        </div>

        <?php if ($is_active) { ?>
          <p text std bold trimt><?= htmlspecialchars($SetBeatmap->version); ?></p>
        <?php } ?>
      </div>

      <?php if (!$is_active) { ?>
        <div ttooltip fl gap=smol alic>
          <div fl gap=smolest alic>
            <mi text midler <?= "beatmap-difficulty-color=" . $SetBeatmap->difficulty(); ?>>star</mi>
            <p text bold <?= "beatmap-difficulty-color=" . $SetBeatmap->difficulty(); ?>>
              <?= number_format(htmlentities($SetBeatmap->diff), 1); ?>
            </p>
          </div>
          <p text bold><?= htmlentities($SetBeatmap->version); ?></p>
        </div>
      <?php } ?>
    </a>
  <?php } ?>
</div>