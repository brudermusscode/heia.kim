<?php

use Bruder\Heiakim\Model\Squad;

/**
 * @var Squad
 */
$Squad = $Notification->reference;

if (!$Squad) :
  include $unavailable;
else : ?>

  <a href="<?= $Squad->link(); ?>">
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
                <p text std>Your squad has changed their name</p>
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
