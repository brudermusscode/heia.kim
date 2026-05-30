<?php

use Bruder\Utils\Utils;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Country;

/**
 * @var int $leaderboard_counter
 * @var int $user_id
 * @var int $pp
 * @var string $mode
 * @var string $mod
 * @var string $type
 * @var string $country
 */

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
 * @var User
 */
$User = User::find($user_id);

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

<box-model filled=darker rankings-user-best has-hover-menu clickable flexone <?= $sizes[$leaderboard_counter][0]; ?>>
  <div z>
    <?php include __DIR__ . "/_dropdown.php"; ?>
  </div>

  <a href="<?= $User->link(); ?>" link></a>

  <bm-inr size=std>

    <picture class=image>
      <?php $User->image(); ?>
    </picture>

    <div class=content>
      <div fl gap=smol alic>
        <p text bold>#<?= $rank; ?></p>
        <a href="<?= "$base_url/$mode/$mod/$type/$Country->abbreviation"; ?>" fl gap=smol z alic>
          <picture size=smoler icon-only circled has-tooltip=bottom fl alic jucc>
            <?php $Country->icon(); ?>
            <div ttooltip>
              <p text std bold><?= $Country->display(); ?></p>
            </div>
          </picture>
        </a>
        <p text bold class=name><?= $User->name(); ?></p>
      </div>

      <div fl jucc gap=smol>
        <mbutton material has-icon=right tag filled=lighter>
          <p text bold><?= $performance; ?></p>
          <i class="mi" size=std><?= $icon; ?></i>
        </mbutton>
        <mbutton material tag>
          <p text bold><?= number_format($Stat->acc ?? 99.000, 2); ?> %</p>
        </mbutton>
      </div>
    </div>

  </bm-inr>
</box-model>

<?php

unset($User, $Stat, $Country);

?>