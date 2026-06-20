<?php

use Heiakim\Time\Time;
use Heiakim\Model\ConnectionDiscord;

/**
 * @var string $category
 * @var string $sub
 * @var string $var
 */

/**
 * @var ?ConnectionDiscord
 */
$Connection = CurrentUser->discord;

?>

<div fl fldircol gap>
  <div fl alic gap>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>
    <p text mid bold>Discord</p>
  </div>

  <div fl fldircol gap>

    <div fl alic gap>
      <mi circled style=height:56px;min-width:56px; background=light wide class="ri-discord-fill" color=discord-blue></mi>
      <div fl fldircol gap=smol>
        <?php if ($Connection) { ?>
          <div fl gap=smol+ alic>
            <mi size=spec color=green>toggle_on</mi>
            <p text><?= __("Active") ?> &middot;
              <strong color=company><?= Time::ago($Connection->created_at); ?></strong>
            </p>
          </div>

          <?php if ($Connection->updated_at) { ?>
            <div fl gap=smol+ alic>
              <mi size=spec>update</mi>
              <p text><?= __("Last accessed") ?> &middot;
                <strong><?= Time::ago($Connection->updated_at, true); ?></strong>
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
    </div>

    <div fl fldircol gap=smol+ outlined=darker rounded p24>
      <p text midler bold><?= __("Information we use") ?></p>
      <div fl fldircol gap=smol>
        <div fl gap=smol+ alic>
          <mi size=spec slight>info</mi>
          <p text><?= __("Username, avatar and banner") ?></p>
        </div>
        <div fl gap=smol+ alic>
          <mi size=spec slight>info</mi>
          <p text><?= __("E-mail address") ?></p>
        </div>
        <div fl gap=smol+ alic>
          <mi size=spec slight>info</mi>
          <p text><?= __("Servers you are in") ?></p>
        </div>
        <div fl gap=smol+ alic>
          <mi size=spec slight>info</mi>
          <p text><?= __("Join servers for you") ?></p>
        </div>
        <div fl gap=smol+ alic>
          <mi size=spec slight>info</mi>
          <p text><?= __("Member info for servers you are in") ?></p>
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