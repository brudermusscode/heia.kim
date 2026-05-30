<?php

use Bruder\Heiakim\Model\User;

/**
 * @var User
 */
$ReferenceUser = $Notification->reference ?? User::find(3);

?>

<div notification class="notif__element" system <?php if (!$Notification->read_at) echo " unread "; ?> frozen>
  <div class=notif__element_inr>
    <picture circled class=notif__element_picture>
      <?php $ReferenceUser->image(); ?>
      <div class=type_badge background=light>
        <mi color=dark>rainy_snow</mi>
      </div>
    </picture>

    <div class="notif__element_content">
      <p text bold><?= $ReferenceUser->name; ?> froze your account</p>
      <p text smol slight><?= $timestamp; ?></p>
    </div>
  </div>

  <a href="/my/game/restriction">
    <div rounded filled=lighter mt=smol color=dynamic clickable>
      <div p12 fl alic gap=smol+>
        <div fl alistart gap=smol+>
          <mi size=smol>info</mi>
          <p text smol>Submit a live play, or your account will get restricted.</p>
        </div>
        <mi std>arrow_forward</mi>
      </div>
    </div>
  </a>
</div>