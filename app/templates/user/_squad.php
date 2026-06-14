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

/**
 * @var ?Squad
 */
$Squad = $User->squad;

if ($Squad) {
  $has_clan = true;

  $SquadUser = $User->squad_user;
} else
  $has_clan = false;

if ($has_clan) :
  $squad_logo = $Squad->logo;
  $squad_headline = $Squad->headline;

?>

  <div fl fldircol gap=smol+>
    <p text bold ttup title-inline>Squad</p>

    <div class=squad_card>
      <a href="<?= "/squad/$Squad->id"; ?>">
        <div ripple-effect rounded=wide posrel clickable>
          <picture class=squad_card__background rounded=wide>
            <?php include HELPER . "/squads/_image_headline.php"; ?>
          </picture>
          <div p24 z>
            <div flcol gap=smol>
              <div fl align-items=center gap flex-truncate>
                <picture circled size=std>
                  <?php include HELPER . "/squads/_image_logo.php"; ?>
                </picture>
                <p text std bold trimt color=white><?= $Squad->name; ?></p>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>

  <?php else :
  if ($is_my_profile) : ?>

    <a href="/squads">
      <box-model outlined mb=std clickable>
        <div pblock48 pinline48>
          <div fl fldircol gap>
            <div fl gap align-items=center>
              <p text std>
                <i class="mi">west</i>
              </p>
              <p text mid>
                <i class="mi">workspaces</i>
              </p>
            </div>
            <p text bold midler><?= __("Find a squad") ?></p>
            <p text smol><?= __("Play together with your friends and climb the leaderboard") ?></p>
          </div>
        </div>
      </box-model>
    </a>

  <?php else : ?>

    <div fl justify-content=center mb style=display:none;>
      <mbutton mid ripple-effect background=special clickable
        data-action="squads:invite"
        data-id="<?= $User->id; ?>">
        <div fl gap=smol+ align-items=center color=special-text>
          <p text mid>
            <i class="mi">arrow_circle_left</i>
          </p>
          <p text std>Invite to squad</p>
        </div>
      </mbutton>
    </div>

  <?php endif; ?>
<?php endif; ?>