<?php

use Heiakim\Model\User;
use Heiakim\Enum\Privilege;

/**
 * @var User
 */
$ReferenceUser = $Notification->reference ?? User::find(3);

/**
 * @var array of Privilege
 */
$Privs = Privilege::by_bits($Notification->reference_2_id);

/**
 * @var bool
 */
$increased = $sub_types[1] === "user+priv+increase";

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
            <p text std><strong><?= $ReferenceUser->name; ?></strong>
              <?= $increased ? "increased" : "decreased"; ?> your privileges</p>
          </div>
          <div>
            <p text std>Your highest rank is <strong><?= $Privs[0]->name; ?></strong>
            </p>
            <p text smol slight><?= $timestamp; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>