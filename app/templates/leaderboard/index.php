<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\Leaderboard;
use Heiakim\Model\Country;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;

/**
 * Mode
 */
$mode = filter_var($_GET["mode"] ?? "osu", FILTER_SANITIZE_SPECIAL_CHARS);
$mode = !in_array($mode, Gamemode::$modes_text) ? "osu" : $mode;

/**
 * Mod
 */
$mod = filter_var($_GET["mod"] ?? "vanilla", FILTER_SANITIZE_SPECIAL_CHARS);
$mod = !in_array($mod, Gamemode::$mods_text) ? "vanilla" : $mod;

$fck_mod = $mod;

/**
 * Leaderboard type
 */
$type = filter_var($_GET["type"] ?? "performance", FILTER_SANITIZE_SPECIAL_CHARS);
$type = !in_array($type, Leaderboard::$types) ? "performance" : $type;

/**
 * Country
 */
$country = filter_var($_GET["country"] ?? "global", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var bool
 */
$is_squad_lb = $country === "squads" ? true : false;

/**
 * Get the gu mode.
 */
$gumode = Gamemode::get_gumode_as_int($mode, $mod);

/**
 * @var string
 */
$sort_by = match ($type) {
  "score" => "rscore",
  default => "pp",
};

/**
 * @var Leaderboard
 */
$Leaderboard = new Leaderboard;

/**
 * Hyperlink build
 */
$link_add_country = $country ? "/$country" : '';
$link_add_type = $type ? "/$type" : '';

/**
 * We've got unknown keys in the leaderboard array since
 * they represent the user's id with a score as the value.
 * Therefore we need a counter that will increase by one
 * whenever a new user from the leaderboard is being
 * worked with.
 */
$leaderboard_counter = 0;

/**
 * Cache newly added countries and store them in the database.
 */
if (!$is_squad_lb)
  $Leaderboard->update_countries();

/**
 * Basication
 */
$base_url = "/leaderboard";

/**
 * Pagination
 */
$ppage  = filter_var($_GET["ppage"] ?? 1, FILTER_VALIDATE_INT);

/**
 * Conditions
 */
$limit = 50;
$offset = $ppage === 1
  ? ($ppage - 1) * $limit
  : ($ppage - 1) * $limit + 1;

/**
 * Include the squad leaderboard, if it is set.
 */
if ($is_squad_lb)
  include __DIR__ . "/pages/_squads.php";
else {

  /**
   * @var array
   */
  $LeaderboardUsers = $Leaderboard->view(
    mode: $gumode,
    country: $country,
    sort: $sort_by,
    limit: $limit - 1,
    offset: $offset ? $offset - 1 : $offset
  );

  /**
   * @var int
   */
  $pcount = $LeaderboardUsers["count"];
  unset($LeaderboardUsers["count"]);

  /**
   * Include header.
   */
  include __DIR__ . "/_header.php";

?>

  <div page-structure="leaderboard">
    <div column-wrapper>

      <div column=small style=top:142px;>
        <?php

        /**
         * Inlcude the type menu.
         */
        include __DIR__ . "/_menu.php"; ?>
      </div>

      <div column=large flexone fl fldircol gap=smol>
        <?php

        /**
         * Include filter.
         */
        include __DIR__ . "/_filter.php"; ?>


        <?php if (!$LeaderboardUsers) { ?>
          <box-model rounded=wide filled=lighter>
            <bm-inr p62>
              <div fl fldircol alic jucc gap>
                <div circled style=min-height:4.2em;width:4.2em; filled fl alic jucc>
                  <i class=mi text wide>face</i>
                </div>
                <div tac>
                  <p text wide bold><?= __("Nothing") ?></p>
                  <p text std><?= __("There are no players on this board.") ?></p>
                </div>
              </div>
            </bm-inr>
          </box-model>

        <?php } else { ?>

          <?php

          /**
           * Only show the very best player when the current page is the
           * first one.
           */
          if ($ppage === 1) { ?>
            <div fl fldircol gap>
              <div fl fldircol gap="smolest" title-inline>
                <p text mid bold><?= __("The Best") ?></p>
              </div>
              <div best>
                <?php

                /**
                 * The first player.
                 */
                foreach ($LeaderboardUsers as $user_id => $pp) {
                  if ($leaderboard_counter == 3) break;

                  include __DIR__ . "/_player_first.php";

                  unset($LeaderboardUsers[$user_id]);
                  $leaderboard_counter++;
                }

                ?>
              </div>
            </div>
          <?php } ?>

          <div fl fldircol gap>
            <div title-inline>
              <p text std><?= __("Showing") ?> <?= $ppage > 1 ? $offset : $offset + 1; ?> -
                <?= $ppage > 1 ? $offset + $limit - 1 : $offset + $limit; ?></p>
            </div>

            <div fl fldircol gap=smol>
              <?php

              /**
               * All other players.
               */
              foreach ($LeaderboardUsers as $user_id => $pp) {
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
                 * @var User
                 */
                $User = User::find($user_id);

                /**
                 * @var object
                 */
                $development = $User->get_rank_development($gumode);

                /**
                 * @var string
                 */
                $development_by_country = match ($country) {
                  "global" => $development["global"][$type],
                  default  => $development["country"][$type],
                };

                /**
                 * @var array
                 */
                $development_options = [
                  "icon" => $development_by_country == 0
                    ? "trending_flat"
                    : ($development_by_country > 0 ? "trending_down" : "trending_up"),
                  "background" => $development_by_country == 0
                    ? "filled=darker"
                    : ($development_by_country > 0 ? "background=unfollow" : "background=follow"),
                  "color" => $development_by_country == 0
                    ? "color=dynamic"
                    : ($development_by_country > 0 ? "color=dark-red" : "color=dark-green"),
                ];

                /**
                 * @var Country
                 */
                $Country = $User->country()->first();

                /**
                 * @var Stat
                 */
                $Stat = $User->stats()
                  ->where("mode", $gumode)
                  ->first();

                /**
                 * @var int
                 */
                $rank = $country == "global"
                  ? $User->get_rankings($gumode)->global
                  : $User->get_rankings($gumode)->country;

              ?>
                <box-model filled=lighter clickable rankings-user has-hover-menu rounded=wide posrel fl>
                  <?php include __DIR__ . "/_dropdown.php"; ?>

                  <a link href="<?= $User->link(); ?>"></a>

                  <picture class=ru__picture rounded=wide style=overflow:hidden;>
                    <?php $User->image(); ?>
                  </picture>

                  <bm-inr class=ru__inr size=smol>
                    <div fl alic gap=smol>
                      <p text bold>#<?= $rank; ?></p>
                      <a href="<?= "$base_url/$mode/$mod/$type/$Country->abbreviation"; ?>" fl gap=smol alic circled z>
                        <picture size=smoler icon-only circled has-tooltip=bottom fl alic jucc>
                          <?php $Country->icon(); ?>
                          <div ttooltip>
                            <p text bold><?= $Country->display(); ?></p>
                          </div>
                        </picture>
                      </a>

                      <?php if ($User->squad) { ?>
                        <div filled=darker rounded="wide" pblock8 pinline4 ttup>
                          <p text smol bold color="dynamic"><?= $User->squad->tag; ?></p>
                        </div>
                      <?php } ?>
                      <p text bold><?= $User->name(); ?></p>
                    </div>

                    <div class=ru__tag_outer>
                      <div fl alic>
                        <div class=ru__tag filled rounded=wide fl alic jucc gap=smol>
                          <p text bold><?= $performance; ?></p>
                          <i class="mi" size=smol+><?= $icon; ?></i>
                        </div>
                        <div class=ru__tag fl alic jucc>
                          <p text bold><?= number_format($Stat->acc ?? 99.000, 2); ?> %</p>
                        </div>
                      </div>
                      <div class=development <?= $development_options["background"] . " " . $development_options["color"]; ?> rounded=wide fl alic jucc>
                        <i class="mi" size=smol+><?= $development_options["icon"]; ?></i>
                      </div>
                    </div>
                  </bm-inr>
                </box-model>
              <?php

                unset($LeaderboardUsers[$user_id]);
                $leaderboard_counter++;
              }

              ?>
            </div>
          </div>

        <?php } ?>

        <div fl jucc>
          <div fl gap=smol flex-wrap=wrap alic>
            <?php

            /**
             * @var string
             */
            $base_url = "$base_url/$mode/$mod/$type/$country";

            include COMPONENT . "/_pagination.php";

            ?>
          </div>
        </div>
      </div>



      <div column=small hide-tablet>

      </div>
    </div>
  </div>

<?php
}

/**
 * Include the page ending with the lovely bird.
 */
include TEMPLATE . "/global/_scroll_end_logo.php";
