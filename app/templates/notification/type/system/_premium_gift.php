<?php

use Bruder\Heiakim\Model\User;

/**
 * @var User
 */
$ReferenceUser = $Notification->reference ?? User::find(3);

?>

<div notification class="notif__element" system <?php if (!$Notification->read_at) echo " unread "; ?>>
  <div class=notif__element_inr>
    <picture circled class=notif__element_picture>
      <?php $ReferenceUser->image(); ?>
      <div class=type_badge background=premium>
        <mi color=premium><?= PREMIUM_ICON; ?></mi>
      </div>
    </picture>

    <div class="notif__element_content">
      <p text bold><?= $ReferenceUser->name; ?> gifted <?= PREMIUM_NAME; ?> to you!</p>
      <p text smol slight><?= $timestamp; ?></p>
    </div>
  </div>
</div>