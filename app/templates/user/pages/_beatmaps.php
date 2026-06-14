<?php

use Heiakim\Model\Squad;
use Heiakim\Model\User;

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

$Beatmaps = $User->most_played_beatmaps($gumode, 18);

?>

<div style=margin-top:22em; content-width=wider fl fldircol gap=smol+>

  <div fl gap alic title-inline>
    <p text bold ttup>Beatmaps</p>
  </div>

  <div class="beatmaps" grid-repeat gap=smol clear-flex style=padding-top:0;>
    <?php

    foreach ($Beatmaps ?? [] as $key => $Beatmap) {
      echo "<div grid-keeper>";
      include COMPONENT . "/beatmaps/_beatmap.php";
      echo "</div>";
    }

    ?>
  </div>
</div>

<?php include TEMPLATE . "/user/_not_implemented.html"; ?>