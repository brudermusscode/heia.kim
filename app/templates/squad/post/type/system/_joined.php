<?php

use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Model\User;
use Heiakim\Time\Time;

/**
 * @var SquadFeedItem $Item
 */

/**
 * @var User
 */
$User = $Item->user;

?>

<div fl fldircol gap=smol+>
  <div fl alic gap=smol slight>
    <mi>handshake</mi>
    <p text bold mid style=margin-top:-6px;>&middot;</p>
    <p text>Joined</p>
  </div>

  <div posrel clickable rounded>
    <a href="<?= $User->link(); ?>">
      <div fl alic gap=smol+ posabs z pinline24 style="top:50%;translate: 0 -50%;" maxw100>
        <picture mid circled>
          <?php $User->image(); ?>
        </picture>
        <div flex-truncate>
          <p text mid bold color=light trimt>
            <?= $User->name(); ?></p>
          <p text color=light>Member &middot;
            <span color=company><?= Time::ago($User->creation_time); ?></span>
          </p>
        </div>
      </div>

      <picture style="width:100%;" rounded ovhid image-fade>
        <?php $User->headline_cover(); ?>
      </picture>
    </a>
  </div>
</div>