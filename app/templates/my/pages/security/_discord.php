<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\Connect\ConnectDiscord;

/**
 * @var ?ConnectDiscord
 */
$Discord = $CurrentUser->discord;

?>

<div content-width=smol>
  <div mt=wide mb fl gap=mid align-items="center" mb=std>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>

    <label size="mid" has-secondary>
      <div class="label__main">
        <p bold><?= __("Connection") ?></p>
      </div>
    </label>
  </div>

  <div fl fldircol gap>
    <box-model filled>

      <box-model filled=darker>
        <bm-inr size=mid fl gap alic>
          <div circled fl alic jucc style=height:4.2em;min-width:4.2em; background=light>
            <mi wider class="ri-discord-fill" color=discord-blue></mi>
          </div>
          <div fl fldircol gap=smol>
            <div fl gap=smol alic>
              <p text midler bold>Discord</p>
            </div>

            <?php if ($Discord) { ?>
              <div fl gap=smol+ alic>
                <mi size=spec color=green>toggle_on</mi>
                <p text><?= __("Active since") ?>
                  <strong><?= Time::ago($Discord->created_at); ?></strong></strong>
                </p>
              </div>

              <?php if ($Discord->updated_at) { ?>
                <div fl gap=smol+ alic>
                  <mi size=spec>update</mi>
                  <p text><?= __("Last accessed") ?>
                    <strong><?= Time::ago($Discord->updated_at, true); ?></strong></strong>
                  </p>
                </div>
              <?php } ?>

            <?php } else { ?>
              <div fl gap=smol+ alic>
                <div style=height:12px;width:12px; circled background=red></div>
                <p text><?= __("Inactive") ?></strong>
                </p>
              </div>
            <?php } ?>
          </div>
        </bm-inr>
      </box-model>

      <bm-inr size=wide fl fldircol gap>
        <div fl fldircol gap=smol+>
          <p text midler bold><?= __("Information we use") ?></p>
          <div fl fldircol gap=smol>
            <div fl gap=smol+ alic>
              <mi size=spec slight>info</mi>
              <p text><?= __("Access your username, avatar and banner") ?></p>
            </div>
            <div fl gap=smol+ alic>
              <mi size=spec slight>info</mi>
              <p text><?= __("Access your e-mail address") ?></p>
            </div>
            <div fl gap=smol+ alic>
              <mi size=spec slight>info</mi>
              <p text><?= __("Know what servers you're in") ?></p>
            </div>
            <div fl gap=smol+ alic>
              <mi size=spec slight>info</mi>
              <p text><?= __("Join servers for you") ?></p>
            </div>
            <div fl gap=smol+ alic>
              <mi size=spec slight>info</mi>
              <p text><?= __("Read your member info for servers you belong to") ?></p>
            </div>
          </div>
        </div>

        <?php if ($Discord) { ?>
          <div fl jucend>
            <form request="connect:delete" redirect="<?= $base_url ?>">
              <input type=hidden name=type value=discord />
              <mbutton material submit-closest background=unfollow color=dark-red>
                <p text bold><?= __("Remove") ?></p>
              </mbutton>
            </form>
          </div>
        <?php } else { ?>
          <div filled=darker p18 rounded>
            <div fl alic gap>
              <mi>privacy_tip</mi>
              <p class="text">
                <?= __("We will <strong>join your account to our Discord server</strong> so you can benefit from your connection accross the functionality of {app-name}") ?>
              </p>
            </div>
          </div>

          <div fl jucend>
            <mbutton material background=follow color=dark-green
              data-action="connect:start"
              data-type=discord>
              <p text bold><?= __("Connect") ?></p>
            </mbutton>
          </div>
        <?php } ?>
      </bm-inr>
    </box-model>
  </div>
</div>