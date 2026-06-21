<?php

use Heiakim\Model\Beatmap;

/**
 * @var Beatmap $Beatmap
 * @var Beatmap\Set $Set
 * @var string $mode
 * @var string $set_id
 * @var string $map_id
 * @var string $current_mod
 */

?>

<div class="s__modes">
  <?php

  foreach ($Beatmaps as $Beatmapp) {
    $icon = match ($mode) {
      "taiko" => 'taiko',
      "ctb" => 'ctb',
      "mania" => 'mania',
      default => 'vanilla',
    };

    /**
     * @var bool
     */
    $is_active = (int) $map_id === (int) $Beatmapp->id;

  ?>

    <a class=version sub ripple-effect href='<?= "/beatmap-set/$Set->id/$Beatmapp->id/$mode/$current_mod"; ?>'
      <?= !$is_active ? "has-tooltip=bottom no-trans-delay" : "active"; ?>>
      <div class="s__modes_option" fl alic>
        <div <?= "beatmap-difficulty-color=" . $Beatmapp->difficulty(); ?>>
          <p class="option_text"><i class="osu-icon osu-<?= $icon; ?>"></i></p>
        </div>

        <?php if ($is_active) { ?>
          <p text std bold trimt><?= htmlspecialchars($Beatmapp->version); ?></p>
        <?php } ?>
      </div>

      <?php if (!$is_active) { ?>
        <div ttooltip fl gap=smol alic>
          <div fl gap=smolest alic>
            <mi text midler <?= "beatmap-difficulty-color=" . $Beatmapp->difficulty(); ?>>star</mi>
            <p text bold <?= "beatmap-difficulty-color=" . $Beatmapp->difficulty(); ?>>
              <?= number_format(htmlentities($Beatmapp->diff), 1); ?>
            </p>
          </div>
          <p text bold><?= htmlentities($Beatmapp->version); ?></p>
        </div>
      <?php } ?>
    </a>
  <?php } ?>
</div>