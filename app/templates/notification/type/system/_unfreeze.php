<?php

use Heiakim\Model\User;

/**
 * @var User
 */
$ReferenceUser = $Notification->reference ?? User::find(3);

?>

<div notification class="notif__element" system <?php if (!$Notification->read_at) echo " unread "; ?> unrestricted>
  <div class=notif__element_inr>
    <picture circled class=notif__element_picture>
      <?php $ReferenceUser->image(); ?>
      <div class=type_badge background=light>
        <mi color=dark>sunny_snowing</mi>
      </div>
    </picture>

    <div class="notif__element_content">
      <p text bold><?= $ReferenceUser->name; ?> unfroze your account!</p>
      <p text smol slight><?= $timestamp; ?></p>
    </div>
  </div>
</div>