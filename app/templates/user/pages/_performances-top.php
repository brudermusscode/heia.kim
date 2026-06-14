<?php

use Illuminate\Support\Collection;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Score;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var object $rankings
 * @var object $rank_development
 * @var string $base_url
 * @var string $sub_page
 * @var string $more
 * @var string $current_mod
 * @var string $mode
 * @var string $mod
 * @var int $gumode
 * @var bool $is_champion
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var bool $both_sides_can_interact_socially
 */

/**
 * @var Collection<Score>
 */
$Scores = $User->scores()
  ->with("beatmap")
  ->whereHas("beatmap", function ($q) {
    $q->where("status", 2);
  })
  ->where("status", 2)
  ->where("mode", $gumode)
  ->orderBy("pp", "DESC")
  ->limit(18)
  ->get();

?>

<div style=margin-top:22em; content-width=wider fl fldircol gap=smol+>

  <div fl gap alic title-inline>
    <div fl alic gap=smol+>
      <p text bold ttup><?= __("Highest performances") ?></p>
      <p text smol bold filled=darker pinline12 pblock6 rounded=wide>🏅 Ranked</p>
    </div>
  </div>

  <div grid-repeat gap=smol>
    <?php

    foreach ($Scores ?? [] as $key => $Score)
      include TEMPLATE . "/score/_score.php";

    ?>
  </div>

</div>

<?php include TEMPLATE . "/user/_not_implemented.html"; ?>