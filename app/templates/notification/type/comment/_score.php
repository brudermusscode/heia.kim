<?php

use Heiakim\Model\Beatmap;
use Heiakim\Model\User;
use Heiakim\Model\Comment;
use Heiakim\Model\Score;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Notification;
use Heiakim\Utils\Str;
use Illuminate\Support\Collection;

/**
 * @var Notification $Notification
 * @var string $unavailable
 */

/**
 * @var Score
 */
$Score = $Notification->reference;

/**
 * @var Collection<Notification>
 */
$AllNotificationsComments = Notification::where("type", "__comment__/score")
  ->where("reference_id", $Score?->id)
  ->whereNot("reference_2_id", CurrentUser->id)
  ->orderBy("created_at", "DESC")
  ->get();

$comment_count = $AllNotificationsComments->count();

# Set all older notifications to be deleted so it will only show the newest one.
# GROUP IT!
foreach ($AllNotificationsComments as $key => $Notif)
  if ($key !== 0)
    $Notif->delete();

/**
 * @var ?Comment
 */
$Comment = Comment::with("user")
  ->where("type", "score")
  ->where("reference_id", $Score?->id)
  ->orderBy("created_at", "DESC")
  ->first();

if (!$Score || !$Comment || !$Comment->user || !$Score->beatmap) :
  include $unavailable;
else :

  /**
   * @var User
   */
  $User = $Comment?->user;

  /**
   * @var Beatmap
   */
  $Beatmap = $Score->beatmap;

  /**
   * @var object
   */
  $notification_mode = Gamemode::gumode_text($Score->mode);

?>

  <a href="/score/<?= $Score->id; ?>">
    <div notification class="notif__element" clickable>
      <div class=notif__element_inr>
        <picture circled class=notif__element_picture>
          <?php $User->image(); ?>
          <div class=type_badge type=social>
            <i class="mi" size=smol><?= $type_icon; ?></i>
          </div>
        </picture>

        <?php

        /**
         * @var string
         */
        $text = $comment_count - 1 > 1 ? "and <strong>" . $comment_count - 1 . "</strong> others" : "";

        ?>

        <div class="notif__element_content">
          <div fl alic jucsb gap>
            <div fl fldircol gap=smoler>
              <p text std><strong><?= $User->name(); ?></strong> <?= $text; ?> commented your score
              </p>
              <p text smol slight>&bdquo;<?= Str::truncate($Comment->comment_string, 120); ?>&ldquo;</p>
              <p text smol slight><?= $timestamp; ?></p>
            </div>
            <mi std>arrow_forward</mi>
          </div>
        </div>
      </div>
    </div>
  </a>

<?php endif;
