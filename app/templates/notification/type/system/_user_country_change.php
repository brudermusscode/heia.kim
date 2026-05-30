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
      <div class=type_badge type=system>
        <mi>public</mi>
      </div>
    </picture>

    <div class="notif__element_content">
      <div fl justify-content=space-between align-items=center>
        <div fl fldircol>
          <div fl gap=smol alic>
            <p text std><strong><?= $ReferenceUser->name; ?></strong> changed your country</p>
          </div>
          <div>
            <p text std>You are now in &nbsp;
              <img style=height:14px;width:14px; circled
                src="<?= IMAGE . "/country-flags/$Notification->message.svg"; ?>" loading=lazy />&nbsp;
              <strong><?= Locale::getDisplayRegion("-" . strtoupper($Notification->message), LOCALE); ?></strong>
            </p>
            <p text smol slight><?= $timestamp; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>