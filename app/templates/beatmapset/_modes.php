<?php

use Heiakim\Model\Beatmap;

/**
 * @var Beatmap $Beatmap
 * @var Beatmap\Set $Set
 */

$more ??= null;

?>

<div hide-800 fl gap=smol alic>
  <div class=hdr__modes fl alic rounded=wide filled=lighter>
    <a sub href="<?= "$base_url/osu/vanilla" . ($more ? "/$more" : ""); ?>">
      <mbutton mid has-icon=left no-hover-shadow filled=lighter show-text-active ripple-effect
        <?= $mode == "osu" ? "active" : ""; ?>>
        <i class="osu-icon osu-vanilla"></i>
        <p text bold><?= __("Standard") ?></p>
      </mbutton>
    </a>

    <a sub href="<?= "$base_url/taiko/vanilla" . ($more ? "/$more" : "");; ?>">
      <mbutton mid has-icon=left no-hover-shadow filled=lighter show-text-active ripple-effect
        <?= $mode == "taiko" ? "active" : ""; ?>>
        <i class="osu-icon osu-taiko"></i>
        <p text bold>Taiko</p>
      </mbutton>
    </a>

    <a sub href="<?= "$base_url/ctb/vanilla" . ($more ? "/$more" : "");; ?>">
      <mbutton mid has-icon=left no-hover-shadow filled=lighter show-text-active ripple-effect
        <?= $mode == "ctb" ? "active" : ""; ?>>
        <i class="osu-icon osu-ctb"></i>
        <p text bold>Catch the Beat</p>
      </mbutton>
    </a>

    <a sub href="<?= "$base_url/mania/vanilla" . ($more ? "/$more" : "");; ?>">
      <mbutton mid has-icon=left no-hover-shadow filled=lighter show-text-active ripple-effect
        <?= $mode == "mania" ? "active" : ""; ?>>
        <i class="osu-icon osu-mania"></i>
        <p text bold>Mania</p>
      </mbutton>
    </a>
  </div>

  <mselect size=mid filled=lighter color=dynamic align=center mselect-type=visible clickable>
    <div class=mselect__inr fl alic gap=smol+>
      <p mselect-visible-value text bold><?= ucfirst($current_mod); ?></p>
      <mi smol>expand_all</mi>
    </div>
    <mselect-dropdown>
      <div get-size>
        <div class=msd__inr>
          <a sub href="<?= "$base_url/$mode/vanilla" . ($more ? "/$more" : ""); ?>">
            <mselect-option mselect-input-value mselect-change-visible-value>
              <p>Vanilla</p>
            </mselect-option>
          </a>

          <?php if (in_array($mode, ["osu", "taiko", "ctb"])) { ?>
            <a sub href="<?= "$base_url/$mode/relax" . ($more ? "/$more" : ""); ?>">
              <mselect-option mselect-input-value mselect-change-visible-value>
                <p>Relax</p>
              </mselect-option>
            </a>
          <?php } ?>

          <?php if (in_array($mode, ["osu"])) { ?>
            <a sub href="<?= "$base_url/$mode/autopilot" . ($more ? "/$more" : ""); ?>">
              <mselect-option mselect-input-value mselect-change-visible-value>
                <p>Autopilot</p>
              </mselect-option>
            </a>
          <?php } ?>
        </div>
      </div>
    </mselect-dropdown>
  </mselect>
</div>