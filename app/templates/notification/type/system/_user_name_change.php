<?php

use Heiakim\Model\User;

/**
 * @var User
 */
$ReferenceUser = $Notification->reference ?? User::find(3);

?>

<div notification class="notif__element" system <?php if (!$Notification->read_at) echo " unread "; ?>>
  <div class=notif__element_inr>
    <picture circled class=notif__element_picture>
      <?php $ReferenceUser->image(); ?>
      <div class=type_badge type=system>
        <mi>arming_countdown</mi>
      </div>
    </picture>

    <div class="notif__element_content">
      <div fl justify-content=space-between align-items=center>
        <div fl fldircol>
          <div fl gap=smol alic>
            <p text std><strong><?= $ReferenceUser->name; ?></strong> changed your name</p>
          </div>
          <div>
            <p text std>You are now called <strong><?= $Notification->message; ?></strong>
            </p>
            <p text smol slight><?= $timestamp; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>