<?php

use Heiakim\Model\Score;
use Heiakim\Model\User;
use Heiakim\Model\Gamemode;

/**
 * @var ?Score
 */
$Score = $Notification->reference;

/**
 * @var ?User
 */
$User = $Notification->reference_2;

/**
 * @var ?Beatmap
 */
$Beatmap = $Score?->beatmap;

if (!$User || !$Beatmap || !$Score) :
  include $unavailable;
else :

  /**
   * @var object
   */
  $notification_mode = Gamemode::gumode_text($Score->mode);

?>

  <div notification class="notif__element">
    <form>
      <div class=notif__element_inr>
        <div class="notif__element_content" flex-truncate>
          <div fl gap alic mb=smol>
            <a href="<?= $User->link(); ?>">
              <picture circled class=notif__element_picture>
                <?php $User->image(); ?>
                <div class=type_badge type=social>
                  <mi><?= $type_icon; ?></mi>
                </div>
              </picture>
            </a>

            <div>
              <p text std>
                <a href="<?= $User->link(); ?>">
                  <strong><?= $User->name(); ?></strong>
                </a>
                loved your score
              </p>
              <p text smol slight><?= $timestamp; ?></p>
            </div>
          </div>
          <a href="<?= "/score/$Score->id"; ?>">
            <div class=notif__score clickable>
              <div class="cover">
                <picture>
                  <?php $Beatmap->set->cover(); ?>
                </picture>
              </div>
              <div class=notif__score_inr>
                <p text std>
                  <i class="ri-exchange-fill"></i> <strong><?= number_format($Score->pp, 0); ?></strong>
                </p>
                <p text std trimt fl align-items=center gap=smol style=line-height:1.4;>
                  <strong><?= $Beatmap->stripped_title(); ?></strong> &bull;
                  <?= $Beatmap->set->artists->first()->name; ?>
                </p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </form>
  </div>

<?php endif;
