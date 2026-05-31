<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\Leaderboard;
use Heiakim\Model\Squad;
use Heiakim\Utils\Utils;
use Predis\Client;

/**
 * @var Leaderboard $Leaderboard
 * @var int $offset
 * @var int $limit
 * @var int $gumode
 */

/**
 * @var Predis\Client
 */
$Redis = $Leaderboard->redis();

/**
 * @var string
 */
$rkey = Leaderboard::$redis_keys["squads"] . ":$gumode" . ($type === "performance" ? "" : ":rscore");
$ranks = $Redis->zrevrange($rkey, $offset, $offset + $limit, "withscores");

$count = 0;

/**
 * Include header.
 */
include dirname(__DIR__) . "/_header.php";

?>

<div page-structure="leaderboard">
  <div column-wrapper>

    <div column=small style=top:142px;>
      <?php

      /**
       * Inlcude the type menu.
       */
      include dirname(__DIR__) . "/_menu.php"; ?>
    </div>

    <div column=large flexone w100 fl fldircol gap=smol>
      <?php

      /**
       * Include filter.
       */
      include dirname(__DIR__) . "/_filter.php";

      if (!$ranks) { ?>
        <box-model rounded=wide filled=lighter>
          <bm-inr p62>
            <div fl fldircol alic jucc gap>
              <div circled style=min-height:4.2em;width:4.2em; filled fl alic jucc>
                <i class=mi text wide>workspaces</i>
              </div>
              <div tac>
                <p text wide bold><?= __("Nothing") ?></p>
                <p text std>No squads have been participating in this leaderboard</p>
              </div>
            </div>
          </bm-inr>
        </box-model>
      <?php } else { ?>
        <div fl fldircol gap flexone>
          <div fl fldircol gap="smolest" title-inline>
            <p text mid bold><?= __("The Best") ?></p>
          </div>
          <div best>
            <?php foreach ($ranks as $squad_id => $pp) {

              /**
               * Only show the first 3.
               */
              if ($count == 3)
                break;

              /**
               * @var Squad
               */
              $Squad = Squad::find($squad_id);

              /**
               * Skip if the mode is disabled for the squad.
               */
              if (!$Squad->gumode_enabled(Gamemode::$mods_int_per_mode[$mode][0]))
                continue;

              /**
               * Increase the counter.
               */
              $count++;

              /**
               * @var array
               */
              $sizes = [
                0 => ["first"],
                1 => ["second"],
                2 => ["third"],
              ];

              /**
               * @var ?string
               */
              $performance = match ($type) {
                "performance" => number_format($pp),
                "score" => Utils::round_with_ending($pp),
                default => 0,
              };

              /**
               * @var ?string
               */
              $icon = match ($type) {
                "performance" => METRIC_ICON,
                "score" => "trending_up",
                default => "check_box_outline_blank",
              };

              /**
               * @var object
               */
              $Stats = (object) $Squad->performance[$gumode];

            ?>

              <box-model filled=darker rankings-user-best has-hover-menu clickable flexone <?= $sizes[$leaderboard_counter][0]; ?>>
                <a href="<?= $Squad->link(); ?>" link></a>

                <bm-inr size=std>

                  <picture class=image>
                    <?php $Squad->logo(); ?>
                  </picture>

                  <div class=content>
                    <div fl gap=smol alic>
                      <p text bold>#<?= $leaderboard_counter + 1; ?></p>
                      <p text bold class=name><?= $Squad->name; ?></p>
                    </div>

                    <div fl jucc gap=smol>
                      <mbutton material has-icon=right tag filled=lighter>
                        <p text bold><?= $performance; ?></p>
                        <mi><?= $icon; ?></mi>
                      </mbutton>
                      <mbutton material tag>
                        <p text bold><?= number_format($Stats->accuracy, 2); ?> %</p>
                      </mbutton>
                    </div>
                  </div>

                </bm-inr>
              </box-model>

            <?php

              unset($ranks[$squad_id]);
              $leaderboard_counter++;
            }

            ?>
          </div>
        </div>

        <div fl fldircol gap=smol>
          <?php foreach ($ranks as $squad_id => $pp) {

            /**
             * @var Squad
             */
            $Squad = Squad::find($squad_id);

            /**
             * Skip if the mode is disabled for the squad.
             */
            if (!$Squad->gumode_enabled(Gamemode::$mods_int_per_mode[$mode][0]))
              continue;

            /**
             * @var array
             */
            $sizes = [
              0 => ["first"],
              1 => ["second"],
              2 => ["third"],
            ];

            /**
             * @var ?string
             */
            $performance = match ($type) {
              "performance" => number_format($pp),
              "score" => Utils::round_with_ending($pp),
              default => 0,
            };

            /**
             * @var ?string
             */
            $icon = match ($type) {
              "performance" => METRIC_ICON,
              "score" => "trending_up",
              default => "check_box_outline_blank",
            };

            /**
             * @var object
             */
            $Stats = (object) $Squad->performance[$gumode];

          ?>

            <box-model filled=lighter clickable rankings-user has-hover-menu rounded=wide posrel fl>
              <a link href="<?= $Squad->link(); ?>"></a>

              <picture class=ru__picture rounded=wide style=overflow:hidden;>
                <?php $Squad->logo(); ?>
              </picture>

              <bm-inr class=ru__inr size=smol>
                <div fl alic gap=smol>
                  <p text bold>#<?= $leaderboard_counter + 1; ?></p>

                  <div filled=darker rounded="wide" pblock8 pinline4 ttup>
                    <p text smol bold color="dynamic"><?= $Squad->tag; ?></p>
                  </div>
                  <p text bold><?= $Squad->name; ?></p>
                </div>

                <div class=ru__tag_outer>
                  <div fl alic>
                    <div class=ru__tag filled rounded=wide fl alic jucc gap=smol>
                      <p text bold><?= $performance; ?></p>
                      <i class="mi" size=smol+><?= $icon; ?></i>
                    </div>
                    <div class=ru__tag fl alic jucc>
                      <p text bold><?= number_format($Stats->accuracy, 2); ?> %</p>
                    </div>
                  </div>
                </div>
              </bm-inr>
            </box-model>

          <?php

            unset($ranks[$squad_id]);
            $leaderboard_counter++;
          }

          ?>
        </div>
      <?php

      } // end if no squads

      ?>
    </div>

    <div column=small hide-tablet>

    </div>
  </div>
</div>