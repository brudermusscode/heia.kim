<?php

use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\User;

/**
 * @var Squad
 */
$Squad = $Notification->reference;

/**
 * @var User
 */
$User = $Notification->reference_2;

if (!$Squad || !$User) :
  include $unavailable;
else : ?>

  <a href="/squad/<?= $Squad->id; ?>">
    <div notification class="notif__element" <?php if (!$Notification->read_at) echo " unread "; ?> clickable>
      <div class=notif__element_inr>
        <picture class=notif__element_picture circled>
          <?php $Squad->logo(); ?>
          <div class=type_badge type=squad>
            <mi><?= $type_icon; ?></mi>
          </div>
        </picture>

        <div class="notif__element_content">
          <div fl justify-content=space-between align-items=center>
            <div fl fldircol gap=smoler>
              <div fl gap=smol alic>
                <p text smol bold background=invert color=invert pinline6 pblock2 rounded><?= $Squad->tag; ?></p>
                <p text std bold><?= $Squad->name; ?></p>
              </div>
              <div>
                <p text std><strong><?= $User->name; ?></strong> approved your request to join</p>
                <p text smol slight><?= $timestamp; ?></p>
              </div>
            </div>
            <mi std>arrow_forward</mi>
          </div>
        </div>
      </div>
    </div>
  </a>

<?php endif;
