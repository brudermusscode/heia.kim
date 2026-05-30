<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Report;

/**
 * @var User $CurrentUser
 */

$ReferenceUser = $Notification->reference;

/**
 * @var ?Report
 */
$Report = $CurrentUser->reports()
  ->where("reference_id", $ReferenceUser?->id)
  ->first();

if (!$Report || !$ReferenceUser) :
  include $unavailable;
else : ?>

  <div notification report data-id="<?= $Notification->id; ?>"
    <?php if (!$Notification->read_at) echo " unread "; ?> data-reference-id="<?= $Notification->reference_id; ?>">
    <div fl gap>
      <picture class=notif__element_picture>
        <img src="<?= EMOJI . "/doge-ban.png"; ?>" />
        <div class=type_badge type=report>
          <mi><?= $type_icon; ?></mi>
        </div>
      </picture>

      <div class="notif__element_content">
        <div style=line-height:1.2em;>
          <p text std trimt bold>You've created a report</p>
          <p text smol slight><?= $timestamp; ?></p>
        </div>
      </div>
    </div>

    <a href="<?= $ReferenceUser->link(); ?>">
      <box-model filled=lighter mt=smol rounded=wide flexone clickable>
        <div p10 style="padding-right:14px;" fl alic jucsb gap=smol+>
          <div fl alic gap=smol>
            <picture size=std circled>
              <?php $ReferenceUser->image() ?>
            </picture>

            <div fl fldircol gap=smolest>
              <p text std bold><?= $ReferenceUser->name; ?></p>
              <p text smol>
                <span slight><?= __("Last active") ?></span>
                &middot;
                <span color=company>
                  <?= Time::ago(date('Y-m-d h:i:s', $ReferenceUser->latest_activity), true); ?>
                </span>
              </p>
            </div>
          </div>

          <mi smol>arrow_forward</mi>
        </div>
      </box-model>
    </a>
  </div>

<?php endif;
