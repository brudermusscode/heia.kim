<?php

use Heiakim\Model\Squad;

/**
 * @var Squad $Squad
 */

$placements = $Squad->placement();

?>

<div placements fl alic gap>
  <p ttup slight text bold hide-tablet>Placement</p>
  <div fl alic jucc filled rounded=wide p12 style=height:51px; clickable>
    <?php if ($Squad->gumode_enabled(0)) { ?>
      <a href="/leaderboard/osu/vanilla/performance/squads" has-tooltip=bottom>
        <div style=min-width:4em; fl alic jucc gap=smol>
          <mi class="osu-icon osu-vanilla"></mi>
          <p text midler bold><?= $placements[0]->performance; ?></p>
        </div>
        <div ttooltip>
          Standart
        </div>
      </a>
    <?php } ?>

    <?php if ($Squad->gumode_enabled(1)) { ?>
      <div dot-divider></div>
      <a href="/leaderboard/ctb/vanilla/performance/squads" has-tooltip=bottom>
        <div style=min-width:4em; fl alic jucc gap=smol>
          <mi class="osu-icon osu-ctb"></mi>
          <p text midler bold><?= $placements[1]->performance; ?></p>
        </div>
        <div ttooltip>
          Catch the Beat
        </div>
      </a>
    <?php } ?>

    <?php if ($Squad->gumode_enabled(2)) { ?>
      <div dot-divider></div>
      <a href="/leaderboard/taiko/vanilla/performance/squads" has-tooltip=bottom>
        <div style=min-width:4em; fl alic jucc gap=smol>
          <mi class="osu-icon osu-taiko"></mi>
          <p text midler bold><?= $placements[2]->performance; ?></p>
        </div>
        <div ttooltip>
          Taiko
        </div>
      </a>
    <?php } ?>

    <?php if ($Squad->gumode_enabled(3)) { ?>
      <div dot-divider></div>
      <a href="/leaderboard/mania/vanilla/performance/squads" has-tooltip=bottom>
        <div style=min-width:4em; fl alic jucc gap=smol>
          <mi class="osu-icon osu-mania"></mi>
          <p text midler bold><?= $placements[3]->performance; ?></p>
        </div>
        <div ttooltip>
          Mania
        </div>
      </a>
    <?php } ?>
  </div>
</div>