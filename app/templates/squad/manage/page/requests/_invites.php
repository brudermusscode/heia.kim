<?php

use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Time\Time;

/**
 * @var Squad $Squad
 */

/**
 * @var ?SquadRequest
 */
$Requests = $Squad
  ->active_requests()
  ->where("type", "invite")
  ->get();

?>

<?php if (!$Requests->count()) { ?>


  <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap flexone none>
    <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
      <i class="mi" size="wide">north_east</i>
    </div>
    <div tac>
      <p text bold wide>Nothing</p>
      <p text std><?= __("You have not invited anyone to join your squad") ?></p>
    </div>
  </box-model>

<?php } else

  foreach ($Requests ?? [] as $Request) {

    /**
     * @var SquadRequest $Request
     */

    /**
     * @var User
     */
    $AffectedUser = $Request->affected_user;

?>

  <box-model rounded=mid filled=lighter>
    <a href="<?= $AffectedUser->link(); ?>">
      <bm-inr size=std fl gap=smol+ alic jucsb>
        <div fl alic gap=smol+>
          <picture size=std circled>
            <?php $AffectedUser->image(); ?>
          </picture>
          <div>
            <p text bold midler><?= $AffectedUser->name(); ?></p>
            <p text>Last active &middot; <span color=company><?= Time::ago($AffectedUser->latest_activity); ?></span></p>
          </div>
        </div>
      </bm-inr>
    </a>
    <div box-floating-actions fl gap=smoler alic jucend>
      <form request="squad:request:delete" reload>
        <input type=hidden name=id value=<?= $Request->id; ?> />
        <mbutton submit-closest material background=besure color=dark-orange size=std icon-only has-tooltip=bottom>
          <mi>remove</mi>
          <div ttooltip>
            <p text bold>Cancel</p>
          </div>
        </mbutton>
      </form>
    </div>
  </box-model>

<?php } ?>