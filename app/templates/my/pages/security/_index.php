<?php

use Heiakim\Application\Cookie;
use Heiakim\Application\Feature;
use Heiakim\Application\Session;
use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Change;
use Heiakim\Model\Session as ModelSession;

/**
 * @var ?Change
 */
$PasswordChange = CurrentUser->password_changes()
  ->select("created_at")
  ->latest()
  ->first();

?>

<div fl fldircol gap>
  <box-model outlined=darker p24>
    <div p12>
      <p text midler bold>Password</p>
    </div>
    <div hoverable p12 rounded=mid
      data-category=<?= $category ?>
      data-sub=password>
      <div fl gap align-items=center justify-content=space-between>
        <div fl gap alic>
          <mi wide>password</mi>
          <p text>
            <?= $PasswordChange ? "Last changed &nbsp;&middot;&nbsp; <span color=company>" . Time::ago($PasswordChange->created_at, true) . "</span>" : "Never updated"; ?>
          </p>
        </div>
        <mi midler>east</mi>
      </div>
    </div>
  </box-model>
</div>


<!--- DEVICES --->
<div fl fldircol gap=smol+>
  <div title-inline>
    <p text midler bold><?= __("Devices") ?></p>
    <p text><?= __("Manage devices you are logged into your account with") ?></p>
  </div>
  <box-model outlined=darker p24 fl fldircol gap=smol+>
    <div>
      <?php

      /**
       * @var int
       */
      $sessions_count = CurrentUser->sessions->count();

      /**
       * @var Session
       */
      $Session =
        CurrentUser
        ->sessions()
        ->where("token", Cookie::get(ModelSession::$persistent_cookies[1]))
        ->first();

      /**
       * Include the current session before all others.
       */
      include TEMPLATE . "/session/_session-card.php";

      if ($sessions_count > 1) { ?>

        <divide horiz mt=smol mb=smol></divide>

      <?php }

      /**
       * @var ?Session
       */
      $Sessions =
        CurrentUser
        ->sessions()
        ->orderByRaw('ISNULL(updated_at), updated_at DESC, created_at DESC')
        ->whereNot("token", Cookie::get(ModelSession::$persistent_cookies[1]))
        ->limit(4)
        ->get();

      /**
       * Loop through all other sessions.
       */
      foreach ($Sessions as $Session)
        include TEMPLATE . "/session/_session-card.php";

      ?>
    </div>

    <?php

    // TODO: Manage all device sessions in my settings.

    if ($sessions_count > 5) { ?>
      <div fl alic jucsb>
        <p text pinline12>And <strong><?= $sessions_count - 5; ?></strong> more</p>
        <div fl jucend>
          <mbutton material filled disabled>
            <p text bold><?= __("Manage all") ?></p>
          </mbutton>
        </div>
      </div>
    <?php } ?>
  </box-model>
</div>

<div fl fldircol gap=smol+>
  <div title-inline>
    <p text midler bold><?= __("Third party") ?></p>
    <p text std><?= __("Manage the data you share between {app-name} and other third party apps") ?></p>
  </div>

  <!--- THIRD PARTY CONNECTIONS --->
  <box-model outlined=darker p24 fl fldircol gap>
    <div fl fldircol gap=smol+>
      <p text bold>Active connections</p>

      <div>
        <?php if (!CurrentUser->connections->count()) { ?>
          <p text slight mt=smol mb12><?= __("Nothing connected") ?></p>
        <?php } else { ?>

          <!--- Discord --->
          <?php if (CurrentUser->discord) { ?>
            <div rounded pinline12 pblock8 fl gap jucsb alic hoverable
              data-category=<?= $category ?>
              data-sub=discord>
              <div fl gap=smol+ alic>
                <mi wide color=discord-blue class="ri-discord-fill"></mi>
                <div>
                  <p text bold>Discord</p>
                  <p text smol>Active &middot; <span color=company><?= Time::ago(CurrentUser->discord->created_at, true); ?></span></p>
                </div>
              </div>

              <mi midler>east</mi>
            </div>
          <?php } ?>

          <!-- Google --->
          <?php if (CurrentUser->google) { ?>
            <div rounded pinline12 pblock8 fl gap jucsb alic hoverable
              data-category=<?= $category ?>
              data-sub=google>
              <div fl gap=smol+ alic>
                <mi wide class="ri-google-fill" color=google-blue></mi>
                <div>
                  <p text bold>Google</p>
                  <p text smol>Active &middot; <span color=company><?= Time::ago(CurrentUser->google->created_at, true); ?></span></p>
                </div>
              </div>

              <mi midler>east</mi>
            </div>
          <?php } ?>

          <!-- Osu --->
          <?php if (CurrentUser->osu) { ?>
            <div rounded pinline12 pblock8 fl gap jucsb alic hoverable
              data-category=<?= $category ?>
              data-sub=osu>
              <div fl gap=smol+ alic>
                <mi text size=mid class="osu-icon osu-outlined" color=osu-pink></mi>
                <div>
                  <p text bold>osu!</p>
                  <p text smol>Active &middot; <span color=company><?= Time::ago(CurrentUser->osu->created_at, true); ?></span></p>
                </div>
              </div>

              <mi midler>east</mi>
            </div>
          <?php } ?>
        <?php } ?>
      </div>
    </div>

    <div fl fldircol gap=smol+ mt12>
      <p text bold><?= __("Add connection") ?></p>

      <div fl gap=smol>
        <?php if (!CurrentUser->discord && Feature::enabled("connect_discord")) { ?>
          <mbutton material has-icon=left filled size=mid
            data-action="connect:start"
            data-type=discord>
            <mi class="ri-discord-fill"></mi>
            <p text bold>Discord</p>
          </mbutton>
        <?php } ?>

        <?php if (!CurrentUser->google && Feature::enabled("connect_google")) { ?>
          <mbutton material has-icon=left filled size=mid
            data-action="connect:start"
            data-type=google>
            <mi class="ri-google-fill"></mi>
            <p text bold>Google</p>
          </mbutton>
        <?php } ?>

        <?php if (!CurrentUser->osu && Feature::enabled("connect_osu")) { ?>
          <mbutton material has-icon=left filled size=mid
            data-action="connect:start"
            data-type=osu>
            <mi class="osu-icon osu-outlined"></mi>
            <p text bold>osu!</p>
          </mbutton>
        <?php } ?>
      </div>

      <div fl alistart gap=smol slight>
        <mi smol style=margin-top:3px;>info</mi>
        <p text>Clicking one of the options will open the authentication window of the third party service in the current browser tab.</p>
      </div>
    </div>
  </box-model>
</div>