<?php

use Heiakim\Model\Squad;
use Heiakim\Model\User;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var object $rankings
 * @var object $rank_development
 * @var string $base_url
 * @var string $sub
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

$Beatmaps = $User->most_played_beatmaps($gumode, 18);

?>

<div page-structure=user fl fldircol gap=smol+>
  <p text bold ttup>Beatmaps</p>

  <div grid-repeat gap=smol>
    <?php

    foreach ($Beatmaps ?? [] as $key => $Beatmap) {
      include TEMPLATE . "/beatmap/_beatmap-row.php";
    }

    ?>
  </div>
</div>

<?php include TEMPLATE . "/user/_not_implemented.html"; ?>