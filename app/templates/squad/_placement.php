<?php

use Heiakim\Model\Squad;

/**
 * @var Squad $Squad
 * @var object $placement
 */

?>

<div placements fl alic gap=smol+>
  <p ttup slight text bold hide-mobile>Placement</p>
  <div fl alic jucc gap=smol+ filled rounded=wide pl16 pr20 style=height:51px; clickable>
    <?php if ($Squad->gumode_enabled(0)) { ?>
      <a href="/leaderboard/squads/osu/vanilla/performance" has-tooltip=bottom>
        <div fl alic jucc gap=smol>
          <mi class="osu-icon osu-vanilla"></mi>
          <p text midler bold color=company><?= $placements[0]->performance; ?></p>
        </div>
        <div ttooltip>Standart</div>
      </a>
    <?php } ?>

    <?php if ($Squad->gumode_enabled(1)) { ?>
      &middot;
      <a href="/leaderboard/squads/ctb/vanilla/performance" has-tooltip=bottom>
        <div fl alic jucc gap=smol>
          <mi class="osu-icon osu-ctb"></mi>
          <p text midler bold color=company><?= $placements[1]->performance; ?></p>
        </div>
        <div ttooltip>Catch the Beat</div>
      </a>
    <?php } ?>

    <?php if ($Squad->gumode_enabled(2)) { ?>
      &middot;
      <a href="/leaderboard/squads/taiko/vanilla/performance" has-tooltip=bottom>
        <div fl alic jucc gap=smol>
          <mi class="osu-icon osu-taiko"></mi>
          <p text midler bold color=company><?= $placements[2]->performance; ?></p>
        </div>
        <div ttooltip>Taiko</div>
      </a>
    <?php } ?>

    <?php if ($Squad->gumode_enabled(3)) { ?>
      &middot;
      <a href="/leaderboard/squads/mania/vanilla/performance" has-tooltip=bottom>
        <div fl alic jucc gap=smol>
          <mi class="osu-icon osu-mania"></mi>
          <p text midler bold color=company><?= $placements[3]->performance; ?></p>
        </div>
        <div ttooltip>Mania</div>
      </a>
    <?php } ?>
  </div>
</div>