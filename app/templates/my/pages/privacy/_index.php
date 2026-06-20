<?php

use Heiakim\Application\Cookie;
use Heiakim\Model\Session;

?>

<div fl fldircol gap=smol+>
  <box-model outlined=darker style=min-width:20em; flexone p24>
    <div fl gap=smol>
      <div p12 fl fldircol gap=smol>
        <p text midler bold>Mailing</p>
        <p text std>Sub- or unsubscribe from mailings that you receive from our services such as newsletter or
          birthday wishes</p>
      </div>
    </div>
    <div fl jucend mt12>
      <mbutton open="privacy:mailing" filled>
        <p text bold>Manage</p>
      </mbutton>
    </div>
  </box-model>

  <box-model outlined=darker style=min-width:20em; flexone fl fldircol p24>
    <div p12 fl fldircol gap=smol>
      <p text midler bold><?= __("Public profile") ?></p>
      <p text std><?= __("Share your skill with others") ?></p>
    </div>
    <div open="privacy:visibility" hoverable p12 rounded=mid>
      <div fl gap align-items=center justify-content=space-between>
        <div fl gap alic>
          <mi wide>
            <?= CurrentUser->privacy->is_public ? "visibility" : "visibility_off"; ?>
          </mi>
          <p text bold color=company>
            <?= CurrentUser->privacy->is_public ? __("Enabled") : __("Disabled"); ?>
          </p>
        </div>
        <mi midler>east</mi>
      </div>
    </div>
  </box-model>
</div>

<div fl fldircol gap=smol+>
  <div title-inline>
    <p text mid bold>Sharing</p>
  </div>

  <box-model outlined=darker flexone p24>
    <div p12 fl fldircol gap=smol>
      <p text midler bold>Uploads</p>
      <p text std>Files that enter our servers from your device</p>
    </div>
    <div open="privacy:images" hoverable p12 rounded=mid posrel fl gap align-items=center justify-content=space-between>
      <div fl gap alic>
        <mi wide><?= CurrentUser->privacy->image_history ? "history" : "history_off"; ?></mi>
        <div>
          <p text bold>Image history
          <p>
          <p text color=company>
            <?= CurrentUser->privacy->image_history ? "Enabled" : "Disabled"; ?>
          <p>
        </div>
      </div>
      <div posrel>
        <mi midler>east</mi>
        <div notification-dot style=top:-4px;right:-4px;></div>
      </div>
    </div>
  </box-model>
</div>

<div fl fldircol gap=smol+>
  <div title-inline>
    <p text mid bold><?= __("Others") ?></p>
  </div>

  <box-model filled p24>
    <div open="privacy:download" disabled hoverable p12 rounded=mid fl gap alic jucsb>
      <div fl gap alic>
        <div fl jucc alic>
          <mi mid>download</mi>
        </div>
        <div>
          <p text bold><?= __("Download data") ?></p>
          <p text><?= __("Not yet available") ?></p>
        </div>
      </div>
      <mi midler>east</mi>
    </div>

    <div open="privacy:removal" rounded=mid p12 hoverable fl gap alic jucsb>
      <div fl gap align-items=center>
        <div fl jucc alic>
          <mi mid>delete</mi>
        </div>
        <div>
          <p text bold><?= __("Delete account") ?></p>
          <p text>Remove your account and data from <?= APP_NAME; ?></p>
        </div>
      </div>
      <div posrel>
        <div notification-dot style=top:-4px;right:-4px;></div>
        <mi midler>east</mi>
      </div>
    </div>
  </box-model>
</div>

<div fl jucend>
  <form data-form="session:delete">
    <input type=hidden name=token value="<?= Cookie::get(Session::$persistent_cookies[1]); ?>" />
    <mbutton mid outlined=darker color=red color=red submit-closest>
      <p text bold><?= __("Logout") ?></p>
    </mbutton>
  </form>
</div>