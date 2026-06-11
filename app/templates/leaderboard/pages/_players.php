<?php

use Heiakim\Model\Country;
use Heiakim\Model\Stat;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;

/**
 * @var int $gumode
 * @var string $country
 * @var string $sort_by
 * @var int $limit
 * @var int $offset
 * @var int $ppage
 */

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

$pcount = $LeaderboardUsers["count"];
unset($LeaderboardUsers["count"]);

# Best players being shown only on first page in a cooler manner.
if ($ppage === 1) { ?>
  <div fl fldircol gap>
    <div fl fldircol gap="smolest" title-inline>
      <p text mid bold><?= __("The Best") ?></p>
    </div>
    <div best>
      <?php

      # First.
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