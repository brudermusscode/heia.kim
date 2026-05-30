<?php

$more ??= null;

?>

<mselect size=std align=center clickable mselect-type=visible>
  <div class=mselect__inr fl alic gap=smol+>
    <p mselect-visible-value text bold><?= ucfirst($current_mod ?? $mod); ?></p>
    <p>
      <i class="mi" size=smol+>expand_all</i>
    </p>
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