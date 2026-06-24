<?php

use Heiakim\Model\Gamemode;
use Heiakim\Utils\Utils;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var SquadUser $SquadUser
 */

$am_only_member = $SquadUser->squad->members->count() < 2;

# Squad will be deleted when CurrentUser is the only member. Otherwise, we delete the
# SquadUser.
$authentication_type = $am_only_member ? "squad:delete" : "squad:user:delete";

?>

<content std minlineauto fl fldircol gap>
  <div fl alic gap>
    <?php include TEMPLATE . "/squad/manage/_back-button.php"; ?>
    <p text mid bold>Leave</p>
  </div>

  <?php if (!$SquadUser->can_leave()) : ?>

    <box-model filled=lighter p42 pt62 fl fldircol gap=smol+ alic>
      <mi wide color=company-text z background=yellow color=dark circled fl alic jucc
        style="height:64px;width:64px;">brightness_alert</mi>
      <div tac fl fldircol gap=smol>
        <p text wide bold>Hold on</p>
        <p text>You are the chief of your squad. For leaving, you need to <strong>transfer your privileges</strong> to another trusted member.</p>
        <div fl alic jucc mt12>
          <a href="/manage/squad/members">
            <mbutton mid background=invert color=invert>
              See members
            </mbutton>
          </a>
        </div>
      </div>
    </box-model>

  <?php else : ?>

    <form data-action="authentication:create" redirect="/home" update-user-references responder>

      <input type=hidden name="type" value="<?= $authentication_type ?>" />

      <div fl fldircol gap>
        <div fl fldircol gap=smol+>
          <div fl fldircol gap=smol>

            <?php if (!$am_only_member) : ?>
              <box-model filled=lighter animation=fade-in fl alic gap=smol+ p32>
                <mi midler circled filled style="height:48px;width:48px;">
                  groups</mi>
                <p text>
                  <strong><?= $Squad->members->count() ?> members</strong> will be left behind
                </p>
              </box-model>
            <?php endif; ?>

            <box-model filled=lighter animation=fade-in p32>
              <div fl gap>
                <mi midler circled filled style="height:48px;min-width:48px;">
                  trending_up</mi>
                <div>
                  <p text bold>Performance contribution</p>
                  <p text>Any numbers you have contributed will be substracted from your squads performance</p>
                </div>
              </div>

              <div style="position:absolute;height:18px;top:5.2em;left:3.4em;width:4px;" rounded background=company></div>

              <div fl fldircol gap=smol mt12>
                <?php

                /**
                 * @var object
                 */
                $Performance = $SquadUser->performance_per_mode();

                foreach ($Performance as $mode => $P) : ?>
                  <div fl alic gap>
                    <mi mid class="osu-icon osu-<?= Gamemode::mode_icon($mode) ?>" circled filled style="height:48px;min-width:48px;">
                    </mi>
                    <div>
                      <p text bold><?= Gamemode::mode_full($mode) ?></p>
                      <p text>
                        <strong><?= number_format($P->performance, 2, ",", ".") ?></strong> <?= METRIC_NAME ?>, <strong><?= Utils::round_with_ending($P->ranked_score) ?></strong> ranked score
                      </p>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </box-model>
          </div>
        </div>

        <tipp-box outlined rounded=mid>
          <mi>info</mi>
          <p text>
            <?= $am_only_member ? "When authentication is done, <strong>this squad will be deleted</strong>, as you are the only member." : "When authentication is done, you are <strong>permanently removed from this squad</strong>. Be really sure about it, as you will be <strong>blocked from joining again</strong> for a specific amount of time." ?>
          </p>
        </tipp-box>

        <div fl jucend>
          <mbutton mid background="besure" color=dark-orange has-icon=left submit-closest>
            <mi>fingerprint</mi>
            <p text bold>Authenticate</p>
          </mbutton>
        </div>
      </div>
    </form>

  <?php endif ?>

</content>