<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\Beatmap\BeatmapRequest;
use Bruder\Heiakim\Enum\BeatmapStatus;

/**
 * @var User $CurrentUser
 */

/**
 * @var ?BeatmapRequest
 */
$BeatmapRequest = $Notification->reference;

/**
 * @var ?Beatmap
 */
$Beatmap = $BeatmapRequest?->beatmap;

/**
 * @var ?User
 */
$AdminUser = $Notification->reference_2;

if (!$Beatmap || !$BeatmapRequest || !$AdminUser) :
  include $unavailable;
else :

  /**
   * Create featured artists.
   */
  $Beatmap->create_featured_artists();

  /**
   * @var object
   */
  $notification_mode = Gamemode::get_mode_as_text($Beatmap->mode);


?>

  <div notification class="notif__element">
    <form>
      <div class=notif__element_inr>
        <div class="notif__element_content" flex-truncate fl fldircol gap=smol>
          <p pinline12 text std>Your requested Beatmap is now
            <strong><?= $Notification->message == BeatmapStatus::RANKED->value ? "🏅 Ranked" : "❤️ Loved"; ?></strong>!
          </p>

          <a href="<?= "/beatmap-set/$Beatmap->set_id/$Beatmap->id/$notification_mode/vanilla"; ?>">
            <div class=notif__score clickable>
              <div class="cover">
                <picture>
                  <?php $Beatmap->set->cover(); ?>
                </picture>
              </div>
              <div class="notif__score_inr tac">
                <p text bold trimt><?= $Beatmap->stripped_title(); ?></p>
                <p text trimt><?= $Beatmap->set->artists->first()->name; ?></p>
              </div>
            </div>
          </a>

          <p pblock12 text smol slight><?= $timestamp; ?></p>
        </div>
      </div>
    </form>
  </div>

<?php endif;
