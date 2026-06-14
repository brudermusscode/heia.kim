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
$Scores = $User->first_place_scores(
  gumode: $gumode,
  order: "pp",
  limit: 18,
);

?>

<div style=margin-top:22em; content-width=wider fl fldircol gap=smol+>
  <div fl gap alic title-inline>
    <div fl alic gap=smol+>
      <p text bold ttup><?= __("First places") ?></p>
      <div fl gap=smoler>
        <p text smol bold filled pinline12 pblock6 rounded=min>🏅 Ranked</p>
        <p text smol bold filled pinline12 pblock6 rounded=min>❤️ Loved</p>
      </div>
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