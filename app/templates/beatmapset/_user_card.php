<?php

use Heiakim\Model\User;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;

/**
 * @var Beatmap $Beatmap
 * @var Beatmap\Set $Set
 * @var Score $Score
 */

if (!isset($rank))
  $rank = 0;

if (!isset($leaderboard_users))
  $leaderboard_users = [];

/**
 * Increase the current rank by one.
 */
$rank++;

/**
 * @var User
 */
$User = $Score->user;

/**
 * @var Country
 */
$Country = $User->country()->first();

/**
 * Add the user to the $leaderboard_users to ensure there is every
 * user shown only once.
 */
array_push($leaderboard_users, $User->id);

?>

<a href="<?= $User->link(); ?>">
  <box-model filled=lighter rankings-user clickable rounded=wide posrel fl>
    <picture class=ru__picture rounded=wide style=overflow:hidden;>
      <?php $User->image(); ?>
    </picture>

    <bm-inr class=ru__inr size=smol>
      <div fl alic gap=smol>
        <?php if ($rank == 1) { ?>
          <mi color=yellow>military_tech</mi>
        <?php } else { ?>
          <p text bold>#<?= $rank; ?></p>
        <?php } ?>
        <picture size=smoler icon-only circled has-tooltip=bottom>
          <?php $Country->icon(); ?>
          <div ttooltip>
            <p text bold><?= $Country->display(); ?></p>
          </div>
        </picture>

        <?php if ($User->squad) { ?>
          <!-- <div filled=darker rounded="wide" pblock8 pinline4 ttup>
        <p text smol bold color="dynamic"><?= $User->squad->tag; ?></p>
      </div> -->
        <?php } ?>
        <p text bold trimt><?= $User->name(); ?></p>
      </div>

      <div class=ru__tag_outer>
        <div fl alic>
          <div class=ru__tag filled rounded=wide fl alic jucc gap=smol>
            <p text bold><?= number_format($Score->pp, 0); ?></p>
            <i class="mi" size=smol+><?= METRIC_ICON; ?></i>
          </div>
          <div class=ru__tag fl alic jucc style="min-width:90px;padding-inline:8px 0;">
            <p text bold><?= number_format($Score->acc, 2); ?> %</p>
          </div>
        </div>
        <div class="mods" filled>
          <?php

          if ($Score->mods()) {
            foreach ($Score->mods() as $key => $mod) {
              if (strtolower($mod->short) === "nc")
                continue;

              /**
               * Continue on Touch Device mod. Want to implement
               * a special view for this.
               */
              if (strtolower($mod->short) == "td")
                continue;

          ?>
              <div class="mod_option" has-tooltip=bottom>
                <p text smol bold <?php if (strtolower($mod->short) == "fl") echo "color=yellow"; ?>>
                  <?= $mod->short; ?></p>
                <div ttooltip>
                  <p text std bold>
                    <?= $mod->full; ?></p>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <div class="mod_option">
              <i class="mi" size=smol>remove</i>
            </div>
          <?php } ?>
        </div>
      </div>
    </bm-inr>
  </box-model>
</a>