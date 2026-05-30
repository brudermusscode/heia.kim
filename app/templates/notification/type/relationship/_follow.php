<?php

/**
 * @var ?User
 */
$FollowingUser = $Notification->reference;

if (!$FollowingUser) :
  include dirname(__DIR__) . "/_source_deleted.php";
else : ?>

  <a href="<?= $FollowingUser->link(); ?>">
    <div notification clickable>
      <div class=notif__element_inr fl alic>
        <picture class=notif__element_picture circled>
          <?php $FollowingUser->image(); ?>
          <div class=type_badge type=social>
            <mi><?= $type_icon; ?></mi>
          </div>
        </picture>

        <div class="notif__element_content">
          <div fl alic jucsb gap>
            <div>
              <p text std>
                <strong><?= $FollowingUser->name(); ?></strong> is now following you
              </p>
              <p text smol slight><?= $timestamp; ?></p>
            </div>
          </div>
        </div>

        <mi std>arrow_forward</mi>
      </div>
    </div>
  </a>

<?php endif; ?>