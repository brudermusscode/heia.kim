<?php

use Heiakim\Model\Squad;
use Heiakim\Time\Time;

/**
 * @var Squad $Squad
 */

?>

<header full=squad scroll-manipulated>
  <div inner>
    <div w100 fl jucend hide-scrolled posrel>
      <mbutton hide-mobile has-icon=left
        <?= $Squad->joinable === 0 ? "background=unfollow color=dark-red" : ($Squad->joinable === 1 ? "background=besure color=dark-orange" : "background=follow color=dark-green"); ?>>
        <mi><?= $Squad->joinable === 0 ? "public_off" : ($Squad->joinable === 1 ? "vpn_lock" : "globe_asia"); ?></mi>
        <p text bold><?= $Squad->display_publicity(); ?></p>
      </mbutton>
      <mbutton show-mobile icon-only
        <?= $Squad->joinable === 0
          ? "background=unfollow color=dark-red"
          : (
            $Squad->joinable === 1
            ? "background=besure color=dark-orange"
            : "background=follow color=dark-green"
          ); ?>>
        <mi><?= $Squad->joinable === 0 ? "public_off" : ($Squad->joinable === 1 ? "vpn_lock" : "globe_asia"); ?></mi>
      </mbutton>
    </div>

    <picture cover>
      <?php $Squad->headline(); ?>
    </picture>

    <picture image>
      <?php $Squad->logo(); ?>
    </picture>

    <div bottom-wrap>
      <div fl fldircol gap=smol>
        <div fl gap=smol+ alic tac>
          <div tag background=special color=light rounded ttup>
            <p text bold><?= $Squad->tag; ?></p>
          </div>
          <p name text bold><?= $Squad->name; ?></p>
        </div>
        <p text hide-scrolled>Created &middot; <span color=company><?= Time::ago($Squad->created_at, true) ?></span></p>
      </div>

      <?php

      /**
       * @var object
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
    </div>
  </div>
</header>