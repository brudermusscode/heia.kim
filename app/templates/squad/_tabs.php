<?php

use Heiakim\Model\Squad;

/**
 * @var Squad $Squad
 * @var string $id
 * @var string $sub
 * @var string $page
 * @var string $base_url
 */

$mode ??= null;
$active_modes = $Squad->modes();

?>

<div fl alic gap=smol>
  <a href="<?= "$base_url"; ?>">
    <mbutton mid filled has-icon=left show-text-active <?php display_active($page, "index"); ?>>
      <mi>stream</mi>
      <p text bold show-active>Feed</p>
    </mbutton>
  </a>

  <?php if ($active_modes->osu === 1) : ?>
    <a href="<?= "$base_url/scores/osu"; ?>">
      <mbutton mid filled has-icon=left show-text-active
        <?php display_active($mode, "osu"); ?>>
        <mi class="osu-icon osu-vanilla"></mi>
        <p text bold show-active><?= __("Standard"); ?></p>
      </mbutton>
    </a>
  <?php endif; ?>

  <?php if ($active_modes->ctb === 1) : ?>
    <a href="<?= "$base_url/scores/ctb"; ?>">
      <mbutton mid filled has-icon=left show-text-active
        <?php display_active($mode, "ctb"); ?>>
        <mi class="osu-icon osu-ctb"></mi>
        <p text bold show-active>Catch the Beat</p>
      </mbutton>
    </a>
  <?php endif; ?>

  <?php if ($active_modes->taiko === 1) : ?>
    <a href="<?= "$base_url/scores/taiko"; ?>">
      <mbutton mid filled has-icon=left show-text-active
        <?php display_active($mode, "taiko"); ?>>
        <mi class="osu-icon osu-taiko"></mi>
        <p text bold show-active>Taiko</p>
      </mbutton>
    </a>
  <?php endif; ?>

  <?php if ($active_modes->mania === 1) : ?>
    <a href="<?= "$base_url/scores/mania"; ?>">
      <mbutton mid filled has-icon=left show-text-active
        <?php display_active($mode, "mania"); ?>>
        <mi class="osu-icon osu-mania"></mi>
        <p text bold show-active>Mania</p>
      </mbutton>
    </a>
  <?php endif; ?>
</div>