<?php

use Heiakim\Time\Time;
use Heiakim\Model\ConnectionOsu;

/**
 * @var string $category
 * @var string $sub
 * @var string $var
 */

/**
 * @var ?ConnectionOsu
 */
$Connection = CurrentUser->osu;

?>

<div fl fldircol gap>
  <div fl alic gap>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>
    <p text mid bold>osu!</p>
  </div>

  <div fl fldircol gap>

    <div fl alic gap>
      <mi circled style=height:56px;min-width:56px; background=light color=osu-pink text semi-bold wider class="osu-icon osu-outlined"></mi>

      <div fl fldircol>
        <?php if ($Connection) : ?>
          <div fl gap=smol alic>
            <mi color=green>toggle_on</mi>
            <p text><?= __("Active") ?> &middot;
              <strong color=company><?= Time::ago($Connection->created_at); ?></strong>
            </p>
          </div>

          <?php if ($Connection->updated_at) { ?>
            <div fl gap=smol alic>
              <mi>update</mi>
              <p text><?= __("Last accessed") ?> &middot;
                <strong color=company><?= Time::ago($Connection->updated_at); ?></strong>
              </p>
            </div>
          <?php } ?>
        <?php else : ?>
          <div fl gap=smol+ alic>
            <div style=height:12px;width:12px; circled background=red></div>
            <p text><?= __("Inactive") ?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div fl fldircol gap=smol+ outlined=darker rounded p24>
      <p text midler bold><?= __("Information we use") ?></p>
      <div fl fldircol gap=smol>
        <div fl gap=smol+ alic>
          <mi size=spec slight>info</mi>
          <p text>Public information, including name & avatar</p>
        </div>

        <div fl gap=smol+ alic>
          <mi size=spec slight>info</mi>
          <p text>Account statistics</p>
        </div>
      </div>
    </div>

    <?php if ($Connection) { ?>
      <div fl jucend>
        <mbutton mid background=unfollow color=dark-red
          request="connection:delete"
          data-provider="<?= $sub ?>"
          um-open="security"
          shadow-submit>
          <p text bold><?= __("Remove") ?></p>
        </mbutton>
      </div>
    <?php } else { ?>
      <div fl jucend>
        <mbutton mid background=follow color=dark-green
          data-action="connection:start"
          data-provider="<?= $sub ?>"
          data-call-action="connect">
          <p text bold><?= __("Connect") ?></p>
        </mbutton>
      </div>
    <?php } ?>
  </div>
</div>