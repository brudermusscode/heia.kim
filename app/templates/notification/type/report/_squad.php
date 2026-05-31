<?php

use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Report;

$Squad = $Notification->reference;

/**
 * @var ?Report
 */
$Report = CurrentUser
  ->reports()
  ->where([
    "report_type" => "squad",
    "reference_id" => $Squad?->id
  ])
  ->first();

if (!$Report || !$Squad) :
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

    <a href="<?= $Squad->link(); ?>">
      <box-model filled=lighter mt=smol rounded=min flexone clickable>
        <bm-inr size=smol fl jucsb alic gap>
          <div fl alic gap=smol+>
            <picture size=std circled>
              <?php $Squad->logo() ?>
            </picture>

            <div fl fldircol gap=smolest>
              <div fl alic gap=smoler>
                <div pblock6 pinline2 filled=darker rounded>
                  <p text smol bold ttup><?= $Squad->tag; ?></p>
                </div>
                <p text std bold><?= $Squad->name; ?></p>
              </div>
              <p text smol color=company>
                Created &middot;
                <?= Time::ago($Squad->created_at, true); ?>
              </p>
            </div>
          </div>
        </bm-inr>
      </box-model>
    </a>
  </div>

<?php endif;
