<?php

use Bruder\Heiakim\Model\Country;

?>

<div alic style="flex:1;" fl jucsb gap=smol+ title-inline flex-wrap>
  <div fl alic gap=smol>
    <a href="<?= "$base_url/$mode/$mod/performance/$country"; ?>">
      <mbutton material filled=lighter has-icon=left <?php if ($type == "performance") echo "active"; ?>>
        <i class="mi"><?= METRIC_ICON; ?></i>
        <p text std bold>Performance</p>
      </mbutton>
    </a>

    <a href="<?= "$base_url/$mode/$mod/score/$country"; ?>">
      <mbutton material filled=lighter has-icon=left <?php if ($type == "score") echo "active"; ?>>
        <i class="mi">trending_up</i>
        <p text std bold>Score</p>
      </mbutton>
    </a>
  </div>

  <?php

  if ($country !== "global" && $country !== "squads") {
    $Country = Country::where("abbreviation", $country)->first();

  ?>
    <a href="<?= "$base_url/$mode/$mod/$type/global"; ?>">
      <mbutton material background="slight" has-icon=left active>
        <i class=mi>remove</i>
        <picture size=smoler circled fl alic jucc>
          <?php $Country->icon(); ?>
        </picture>
        <p text std bold><?= $Country->display(); ?></p>
      </mbutton>
    </a>
  <?php } ?>

  <div posrel>
    <sub-stick-menu>
      <a <?= $fck_mod == "vanilla" ? "active" : ""; ?> href="<?= "$base_url/$mode/vanilla/$type/$country"; ?>">
        <div class=sm__option hoverable>
          <mi>done</mi>
          <p text bold>Vanilla</p>
        </div>
      </a>

      <?php if (in_array($mode, ["osu", "taiko", "ctb"])) { ?>
        <a <?php if ($mod === "relax") echo "active"; ?> href="<?= "$base_url/$mode/relax/$type/$country"; ?>">
          <div class=sm__option hoverable>
            <mi>done</mi>
            <p text bold>Relax</p>
          </div>
        </a>
      <?php } ?>

      <?php if (in_array($mode, ["osu"])) { ?>
        <a <?php if ($mod === "autopilot") echo "active"; ?> href="<?= "$base_url/$mode/autopilot/$type/$country"; ?>">
          <div class=sm__option hoverable>
            <mi>done</mi>
            <p text bold>Autpilot</p>
          </div>
        </a>
      <?php } ?>
    </sub-stick-menu>
  </div>
</div>