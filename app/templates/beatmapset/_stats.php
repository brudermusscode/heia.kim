<?php

use Heiakim\Model\Beatmap;

/**
 * @var Beatmap $Beatmap
 * @var Beatmap\Set $Set
 */

?>

<box-model outlined>
  <bm-inr size=std>
    <div fl mb=std alic gap>

      <mbutton filled has-icon=left has-tooltip=bottom>
        <mi size=midler>play_circle</mi>
        <p text bold>
          <?= number_format($Beatmap->plays); ?>
        </p>
        <div ttooltip>
          <p text std bold><?= __("Times played") ?></p>
        </div>
      </mbutton>

      <div has-tooltip=bottom>
        <p text semi-bold>
          <?php

          $duration = gmdate("H:i:s", $Beatmap->total_length);

          if (preg_match("/^00:/", $duration)) {
            echo substr($duration, 3, 10);
          } else {
            echo $duration;
          }

          ?>
        </p>
        <div ttooltip>
          <p text std bold><?= __("Duration") ?></p>
        </div>
      </div>

      <div has-tooltip=bottom>
        <p text semi-bold>
          <?= number_format($Beatmap->bpm); ?> bpm
        </p>
        <div ttooltip>
          <p text std bold><?= __("Beats per minute") ?></p>
        </div>
      </div>
    </div>

    <div fl fldircol gap=smol>
      <?php foreach ($Beatmap->stats() as $stat) { ?>
        <div fl fldircol style=gap:.1em;>
          <p text smol bold><?= $stat[1]; ?></p>
          <div beatmap-difficulty-bg=<?= $stat[2]; ?>
            style="width:calc(<?= $stat[0]; ?>% * 10);max-width:100%;min-width:3.52em;" rounded=mid>
            <div pinline12 pblock2 style=text-align:right;>
              <p text smol bold><?= number_format($stat[0], 2); ?></p>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </bm-inr>
</box-model>