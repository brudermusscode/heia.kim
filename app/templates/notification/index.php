<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Notification;

require _root() . "/config/get_requirements.php";

/**
 * @var User $CurrentUser
 */

authorize(resource: $CurrentUser);

/**
 * @var string
 */
$category = filter_var(GET("category") ?? "all", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Update last opened notifications.
 */
$CurrentUser->touch_notifications();

/**
 * @var Notification
 */
$Notifications =
  Notification::where("user_id", $CurrentUser->id)
  ->orWhere([
    "type" => "__system__",
    "user_id" => 0,
  ])
  ->orderBy("created_at", "DESC")
  ->get();

/**
 * @var int
 */
$count = 0;

/**
 * Begin output buffer.
 */
ob_start();

if (!$Notifications->count()) : ?>

  <box-model rounded="wide" filled p62 fl fldircol alic jucc gap style="height:100%;" flexone>
    <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled=darker>
      <mi wide>notifications_off</mi>
    </div>
    <div tac>
      <p text bold mid>Nothing</p>
      <p text>No notifications to show</p>
    </div>
  </box-model>

  <?php else :

  /**
   * @var string
   */
  $file_path = __DIR__ . "/category/_$category.php";
  $unavailable = __DIR__ . "/type/_unavailable.php";

  include file_exists($file_path) ? $file_path : TEMPLATE . "/notification/category/_all.php";

  /**
   * Each category file will count the notifications. If
   * there are none for any category, show the Nothing container.
   * @var int $count
   */

  if (!$count) : ?>

    <box-model rounded="wide" filled p62 fl fldircol alic jucc gap style="height:100%;" flexone>
      <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled=darker>
        <mi wide>notifications_off</mi>
      </div>
      <div tac>
        <p text bold mid>Nothing</p>
        <p text>No notifications to show</p>
      </div>
    </box-model>

  <?php endif; ?>
<?php endif; ?>

<?php request_success(data: ob_get_clean(), die: true);
