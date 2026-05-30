<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\Connect\ConnectOsu;

/**
 * @var ?ConnectOsu
 */
$Osu = $CurrentUser->osu;

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
            <mi color=osu-pink text semi-bold wider class="osu-icon osu-outlined"></mi>
          </div>
          <div fl fldircol gap=smol>
            <div fl gap=smol alic>
              <p text midler bold>osu!</p>
            </div>

            <?php if ($Osu) { ?>
              <div fl gap=smol+ alic>
                <mi size=spec color=green>toggle_on</mi>
                <p text><?= __("Active since") ?>
                  <strong><?= Time::ago($Osu->created_at); ?></strong></strong>
                </p>
              </div>

              <?php if ($Osu->updated_at) { ?>
                <div fl gap=smol+ alic>
                  <mi size=spec>update</mi>
                  <p text><?= __("Last accessed") ?>
                    <strong><?= Time::ago($Osu->updated_at, true); ?></strong></strong>
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
              <p text>Access your public information, including name & avatar</p>
            </div>

            <div fl gap=smol+ alic>
              <mi size=spec slight>info</mi>
              <p text>Access your account statistics</p>
            </div>
          </div>
        </div>

        <?php if ($Osu) : ?>
          <div fl jucend>
            <form request="connect:delete" redirect="<?= $base_url ?>">
              <input type=hidden name=type value=osu />
              <mbutton material submit-closest background=unfollow color=dark-red>
                <p text bold><?= __("Remove") ?></p>
              </mbutton>
            </form>
          </div>
        <?php else : ?>
          <div fl jucend>
            <mbutton material background=follow color=dark-green
              data-action="connect:start"
              data-type=osu>
              <p text bold><?= __("Connect") ?></p>
            </mbutton>
          </div>
        <?php endif; ?>
      </bm-inr>
    </box-model>
  </div>
</div>