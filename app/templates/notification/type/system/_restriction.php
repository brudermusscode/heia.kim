<?php

use Heiakim\Model\User;

/**
 * @var User
 */
$ReferenceUser = $Notification->reference ?? User::find(3);

/**
 * @var ?Restriction
 */
$Restriction = $Notification->reference_2;

if (!$Restriction) :
  include $unavailable;
else : ?>

  <div notification class="notif__element" system <?php if (!$Notification->read_at) echo " unread "; ?> restricted>
    <div class=notif__element_inr>
      <picture circled class=notif__element_picture>
        <?php $ReferenceUser->image(); ?>
        <div class=type_badge background=light>
          <mi color=dark>raven</mi>
        </div>
      </picture>

      <div class="notif__element_content">
        <p text bold><?= $ReferenceUser->name; ?> restricted your account</p>
        <?php if ($Restriction && $Restriction->reason) { ?>
          <p text smol color=dark-red bold slight>Reason: &bdquo;<?= $Restriction->reason; ?>&ldquo;</p>
        <?php } ?>
        <p text smol slight><?= $timestamp; ?></p>
      </div>
    </div>

    <?php if (CurrentUser->is_restricted()) { ?>
      <a href="/my/game/restriction">
        <div rounded filled mt=smol color=dynamic clickable>
          <div p12 fl alic gap=smol+>
            <div fl alistart gap=smol+>
              <mi size=smol>info</mi>
              <p text smol>See more information about your restriction & learn how to appeal
              </p>
            </div>
            <mi std>arrow_forward</mi>
          </div>
        </div>
      </a>
    <?php } ?>
  </div>

<?php endif;
