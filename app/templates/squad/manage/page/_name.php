<?php

use Heiakim\Time\Time;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var Squad $Squad
 * @var SquadUser $SquadUser
 */

$disabled = !$Squad->can_change_name() ? "disabled" : "";
$is_disabled = !$Squad->can_change_name();

?>

<content std minlineauto fl fldircol gap>
  <div fl alic gap>
    <?php include TEMPLATE . "/squad/manage/_back-button.php"; ?>
    <p text mid bold>Tag & Name</p>
  </div>

  <div fl fldircol gap>
    <form request="squad:update" redirect="<?= $base_url ?>" responder>
      <box-model filled=lighter>
        <bm-inr size=wide fl fldircol gap>
          <div fl gap=smol>
            <div input material has-icon style=max-width:10em; <?= $disabled; ?>>
              <mi>label</mi>
              <input maxlength="6" type="text" name=tag placeholder="<?= $Squad->tag; ?>" value="<?= $Squad->tag; ?>" />
            </div>

            <div input material has-icon style=flex:1; <?= $disabled; ?>>
              <mi>text_fields</mi>
              <input maxlength="16" type="text" name=name placeholder="<?= $Squad->name; ?>" value="<?= $Squad->name; ?>" />
            </div>
          </div>

          <div fl fldircol gap=smol>
            <p text std bold>Inform members</p>
            <div fl gap align-items=start>
              <p text std>Checking this, will send out a notification to all members and inform them about the changed
                name and/or tag.</p>
              <toggle-switch toggled="true">
                <div class="toggle_switch__inr">
                  <div class="toggle_switch__switcher">
                  </div>
                  <input type="hidden" name="notification" value=1 />
                  <div fl fldirrow justify-content="center">
                    <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </div>
              </toggle-switch>
            </div>

            <div fl jucend>
              <mbutton mid ripple-effect confirm-submit-button background=follow color=dark <?= $disabled; ?>>
                <div action>
                  <?php if (!$is_disabled) { ?>
                    <p text bold>Save</p>
                  <?php } else { ?>
                    <p text bold><?= Time::left($Squad->time_left_for_name_change()); ?></p>
                  <?php } ?>
                </div>
                <div confirmation>
                  <p text bold>Are you sure?</p>
                </div>
              </mbutton>
            </div>
          </div>
        </bm-inr>
      </box-model>
    </form>
  </div>
</content>