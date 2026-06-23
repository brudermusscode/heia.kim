<?php

use Heiakim\Model\Gamemode;

/**
 * @var string $base_url
 * @var string $mode
 * @var string $mod
 * @var array $valid_mods
 */

?>

<mode-menu>
  <jump-menu mm-menu filled="lighter" elevated color="dynamic">
    <div jm-inr>
      <a href="<?= "$base_url/$mode/vanilla" ?>">
        <div ripple-effect class="jm__option" hoverable>
          <p text>Vanilla</p>
          <?php if ($mod === "vanilla") { ?>
            <div class=jm__option_right_icon>
              <mi color=company>done</mi>
            </div>
          <?php } ?>
        </div>
      </a>
      <a href="<?= "$base_url/$mode/relax" ?>"
        <?= !in_array("relax", $valid_mods) ? "disabled" : "" ?>>
        <div ripple-effect class="jm__option" hoverable>
          <div fl jucsb flone alic gap=smoler>
            <p text>Relax</p>
            <?php if (!in_array("relax", $valid_mods)) : ?>
              <p text smol color=red>Not available</p>
            <?php endif; ?>
          </div>
          <?php if ($mod === "relax") { ?>
            <div class=jm__option_right_icon>
              <mi color=company>done</mi>
            </div>
          <?php } ?>
        </div>
      </a>
      <a href="<?= "$base_url/$mode/autopilot" ?>"
        <?= !in_array("autopilot", $valid_mods) ? "disabled" : "" ?>>
        <div ripple-effect class="jm__option" hoverable>
          <div fl jucsb flone alic gap=smoler>
            <p text>Autopilot</p>
            <?php if (!in_array("autopilot", $valid_mods)) : ?>
              <p text smol color=red>Not available</p>
            <?php endif; ?>
          </div>
          <?php if ($mod === "autopilot") { ?>
            <div class=jm__option_right_icon>
              <mi color=company>done</mi>
            </div>
          <?php } ?>
        </div>
      </a>

      <divide horiz mblock4></divide>

      <a href="<?= "$base_url/osu/$mod" ?>">
        <div ripple-effect class="jm__option" hoverable>
          <mi class="osu-icon osu-vanilla"></mi>
          <p text><?= __("Standard"); ?></p>
          <?php if ($mode === "osu") { ?>
            <div class=jm__option_right_icon>
              <mi color=company>done</mi>
            </div>
          <?php } ?>
        </div>
      </a>
      <a href="<?= "$base_url/taiko/$mod" ?>">
        <div ripple-effect class="jm__option" hoverable>
          <mi class="osu-icon osu-taiko"></mi>
          <p text>Taiko</p>
          <?php if ($mode === "taiko") { ?>
            <div class=jm__option_right_icon>
              <mi color=company>done</mi>
            </div>
          <?php } ?>
        </div>
      </a>
      <a href="<?= "$base_url/ctb/$mod" ?>">
        <div ripple-effect class="jm__option" hoverable>
          <mi class="osu-icon osu-ctb"></mi>
          <p text>Catch the Beat</p>
          <?php if ($mode === "ctb") { ?>
            <div class=jm__option_right_icon>
              <mi color=company>done</mi>
            </div>
          <?php } ?>
        </div>
      </a>
      <a href="<?= "$base_url/mania/$mod" ?>">
        <div ripple-effect class="jm__option" hoverable>
          <mi class="osu-icon osu-mania"></mi>
          <p text>Mania</p>
          <?php if ($mode === "mania") { ?>
            <div class=jm__option_right_icon>
              <mi color=company>done</mi>
            </div>
          <?php } ?>
        </div>
      </a>
    </div>
  </jump-menu>

  <mbutton wide mm-open has-icon=left elevated=mid background=company color=company-text>
    <div mm-open-loading>
      <?php include COMPONENT . "/dot-loader.html"; ?>
    </div>
    <mi class="osu-icon osu-<?= Gamemode::mode_icon($mode) ?>"></mi>
    <div fl alic gap=smoler>
      <p text bold><?= Gamemode::mode_full($mode) ?></p>
      <p text slight><?= ucfirst($mod) ?></p>
    </div>
  </mbutton>
</mode-menu>