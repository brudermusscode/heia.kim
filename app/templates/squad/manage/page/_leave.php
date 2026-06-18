<?php

use Heiakim\Model\Gamemode;
use Heiakim\Utils\Utils;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var SquadUser $SquadUser
 */

?>

<content std minlineauto>
  <div fl fldircol gap=smoler>
    <div mt=wide mb fl gap=mid align-items="center" mb=std>
      <?php include TEMPLATE . "/squad/manage/_back-button.php"; ?>
      <p text mid bold>Leave <?= $Squad->name ?></p>
    </div>

    <?php if (!$SquadUser->can_leave()) : ?>
      <box-model filled p42 pt62 fl fldircol gap=smol+ alic>
        <div z background=yellow color=dark circled fl alic jucc
          style="height:4.2em;width:4.2em;">
          <mi wide>brightness_alert</mi>
        </div>
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
      <form
        data-form="authentication:create"
        data-type="squad:user:delete"
        data-redirect="/"
        update-user-references>
        <div fl fldircol gap>
          <div fl fldircol gap=smol+>
            <div>
              <p text midler bold>Are you sure about this?</p>
              <p text>When leaving your squad, there are some things you need to consider</p>
            </div>

            <div fl fldircol gap=smol>
              <box-model filled=lighter animation=fade-in>
                <bm-inr fl gap alic>
                  <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
                    <mi midler>groups</mi>
                  </div>
                  <p text><strong><?= $Squad->members->count() ?> members</strong> will be left behind</p>
                </bm-inr>
              </box-model>

              <box-model filled=lighter animation=fade-in>
                <bm-inr fl fldircol gap=smol+ posrel>

                  <div fl gap>
                    <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
                      <mi midler>trending_up</mi>
                    </div>
                    <div>
                      <p text bold>Performance contribution</p>
                      <p text>Any numbers you have contributed for modes relevant to this squad</p>
                    </div>
                  </div>

                  <div style="position:absolute;height:.8em;top:5em;left:3.4em;width:4px;" rounded background=slight></div>

                  <div fl fldircol gap=smol>
                    <?php

                    /**
                     * @var object
                     */
                    $Performance = $SquadUser->performance_per_mode();

                    foreach ($Performance as $mode => $P) : ?>
                      <div fl alic gap>
                        <div z filled=darker circled fl alic jucc style="height:3.2em;width:3.2em;">
                          <i text mid class="osu-icon osu-<?= Gamemode::mode_icon($mode) ?>"></i>
                        </div>
                        <div>
                          <p text bold><?= Gamemode::mode_full($mode) ?></p>
                          <p text>
                            <strong><?= number_format($P->performance, 2, ",", ".") ?></strong> <?= METRIC_NAME ?>, <strong><?= Utils::round_with_ending($P->ranked_score) ?></strong> ranked score
                          </p>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>

                </bm-inr>
              </box-model>
            </div>
          </div>

          <tipp-box outlined rounded=mid>
            <mi>info</mi>
            <p text>When authentication is done, you are <strong>permanently removed from this squad</strong>. Be really sure
              about it, as you will be <strong>blocked from joining again</strong> for a specific amount of time.</p>
          </tipp-box>

          <div fl jucend>
            <mbutton mid background="besure" color=dark-orange has-icon=left submit-closest>
              <mi>fingerprint</mi>
              <p text bold>Authenticate for leaving</p>
            </mbutton>
          </div>
        </div>
      </form>
    <?php endif ?>
  </div>
</content>