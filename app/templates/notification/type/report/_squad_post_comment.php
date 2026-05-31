<?php

use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Report;

$Comment = $Notification->reference;

/**
 * @var ?Report
 */
$Report = CurrentUser
  ->reports()
  ->where([
    "report_type" => "squad_post_comment",
    "reference_id" => $Comment?->id
  ])
  ->first();

if (!$Report || !$Comment) :
  include $unavailable;
else : ?>

  <div notification report data-id="<?= $Notification->id; ?>" <?php if (!$Notification->read_at) echo " unread "; ?> data-reference-id="<?= $Notification->reference_id; ?>">
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

    <box-model filled=lighter mt=smol rounded=min flexone clickable>
      <bm-inr size=smol fl jucsb alic gap>
        <div fl alic gap=smol+>
          <div fl fldircol gap=smolest>
            <p text std bold>Squad Post Comment &middot; ID: <?= $Comment->id; ?></p>
            <p text smol color=company>
              Created &middot;
              <?= Time::ago($Comment->created_at, true); ?>
            </p>
          </div>
        </div>
      </bm-inr>
    </box-model>
  </div>

<?php endif;
