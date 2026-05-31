<?php

use Heiakim\Time\Time;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Model\User;

/**
 * @var SquadRequest $Request
 */

/**
 * @var User
 */
$User = $Request->user;

?>

<box-model filled squad-request>
  <a href="<?= $User->link(); ?>">
    <bm-inr size=std>
      <div fl align-items=center justify-content=space-between>
        <div fl gap align-items=center>
          <picture size=std circled>
            <?php $User->image(); ?>
          </picture>

          <div fl alic gap=smoler>
            <p text std bold><?= $User->name(); ?></p>
            &middot;
            <p text color=company><?= Time::ago($Request->created_at, true); ?></p>
          </div>
        </div>
      </div>
    </bm-inr>
  </a>

  <div box-floating-actions fl gap=smol alic jucend>
    <form request="squad:request:accept" reload>
      <input type=hidden name=id value="<?= $Request->id; ?>" />
      <mbutton material submit-closest has-icon=left background=follow color=dark-green>
        <mi>done_all</mi>
        <p text bold>Accept</p>
      </mbutton>
    </form>
    <div has-tooltip=bottom>
      <form request="squad:request:delete" reload>
        <input type=hidden name=id value="<?= $Request->id; ?>" />
        <mbutton submit-closest background=besure color=dark-orange icon-only material>
          <mi>remove</mi>
        </mbutton>
      </form>
      <div ttooltip>
        <p text std bold>Decline</p>
      </div>
    </div>
  </div>
</box-model>