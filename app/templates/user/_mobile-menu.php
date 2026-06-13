<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;

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

?>

<div class="mobile_menu">
  <div class="mobile_menu__inr">
    <a href="/home">
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="ri-arrow-left-line"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>Back</p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "/u/$User->id/osu/$mod"; ?>" sub <?php if ($mode === "osu") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-vanilla"></i></p>
          </div>
          <div class=text>
            <p text smol ttup><?= __("Standard") ?></p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "/u/$User->id/taiko/$mod"; ?>" sub <?php if ($mode === "taiko") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-taiko"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>Taiko</p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "/u/$User->id/ctb/$mod"; ?>" sub <?php if ($mode === "ctb") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-ctb"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>CTB</p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "/u/$User->id/mania/$mod"; ?>" sub <?php if ($mode === "mania") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-mania"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>Mania</p>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>