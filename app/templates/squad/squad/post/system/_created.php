<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var User $User
 * @var Squad $Squad
 * @var SquadFeedItem $Item
 */

?>

<div fl fldircol gap=smol>
  <div fl alic gap=smol>
    <mi slight>draw_abstract</mi>
    <p text bold slighter mid style=margin-top:-.2em;>&middot;</p>
    <p text slight>Squad</p>
  </div>

  <p text midler bold mb=smol>Created this squad</p>

  <div outlined ovhid rounded=midler>
    <picture style="padding-top:0%;width:100%;" posrel>
      <div background=hover style="z-index:2;position:absolute;top:0;left:0;height:100%;width:100%;"></div>
      <div style="position:absolute;top:0;left:0;height:100%;width:100%;">
        <?php $Squad->headline(); ?>
      </div>
      <div fl fldircol gap=smol p32 z>
        <picture size=wider circled>
          <?php $Squad->logo(); ?>
        </picture>
        <div fl alic gap=smol>
          <div tag filled="darker" pblock12 pinline8 rounded="wide" ttup>
            <p text bold><?= $Squad->tag; ?></p>
          </div>
          <p text wide bold color=light><?= $Squad->name; ?></p>
        </div>
      </div>
    </picture>
  </div>
</div>