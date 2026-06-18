<?php

use Heiakim\Application\Cookie;
use Heiakim\Application\Feature;
use Heiakim\Application\Session;
use Heiakim\Time\Time;
use Heiakim\Model\Change;
use Heiakim\Model\Session as ModelSession;
use Illuminate\Support\Collection;

/**
 * @var string $category
 */

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
    <p p12 text midler bold>Password</p>
    <div open="security:password" hoverable p12 rounded=mid>
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
  <p text midler bold title-inline><?= __("Your Devices") ?></p>
  <box-model outlined=darker p24 fl fldircol gap=smol+>
    <div>
      <?php

      $sessions_count = CurrentUser->sessions->count();

      /**
       * @var Session
       */
      $Session = CurrentUser->sessions()
        ->where("token", Cookie::get(ModelSession::$persistent_cookies[1]))
        ->first();

      include TEMPLATE . "/session/_session-card.php";

      if ($sessions_count > 1) : ?>
        <divide horiz mt=smol mb=smol></divide>
      <?php endif;

      /**
       * @var Collection<Session>
       */
      $Sessions = CurrentUser->sessions()
        ->orderByRaw('ISNULL(updated_at), updated_at DESC, created_at DESC')
        ->whereNot("token", Cookie::get(ModelSession::$persistent_cookies[1]))
        ->limit(4)
        ->get();

      foreach ($Sessions as $Session)
        include TEMPLATE . "/session/_session-card.php"; ?>
    </div>

    <?php

    // TODO: Manage all device sessions in my settings.

    if ($sessions_count > 5) { ?>
      <div fl alic jucsb>
        <p text pinline12>And <strong><?= $sessions_count - 5; ?></strong> more</p>
        <div fl jucend>
          <mbutton filled disabled>
            <p text bold><?= __("Manage all") ?></p>
          </mbutton>
        </div>
      </div>
    <?php } ?>
  </box-model>
</div>

<div fl fldircol gap=smol+>
  <p text midler bold title-inline><?= __("Third party connections") ?></p>

  <!--- THIRD PARTY CONNECTIONS --->
  <box-model outlined=darker p24 fl fldircol gap>
    <?php

    # Only show this section if the CurrentUser has any active Connection.
    if (CurrentUser->osu || CurrentUser->discord || CurrentUser->github) : ?>
      <div fl fldircol gap=smol+>
        <p text bold>Active connections</p>

        <div>
          <?php if (!CurrentUser->connections->count()) { ?>
            <p text slight mt=smol mb12><?= __("Nothing connected") ?></p>
          <?php } else { ?>

            <!-- Osu --->
            <?php if (CurrentUser->osu) { ?>
              <div open="security:osu!" rounded pinline12 pblock8 fl gap jucsb alic hoverable>
                <div fl gap=smol+ alic>
                  <mi wide class="osu-icon osu-outlined" color=osu-pink></mi>
                  <div>
                    <p text bold>osu!</p>
                    <p text smol>Active &middot; <span color=company><?= Time::ago(CurrentUser->osu->created_at, true); ?></span></p>
                  </div>
                </div>

                <mi midler>east</mi>
              </div>
            <?php } ?>

            <!--- Discord --->
            <?php if (CurrentUser->discord) { ?>
              <div open="security:discord" rounded pinline12 pblock8 fl gap jucsb alic hoverable>
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

            <!-- Github --->
            <?php if (CurrentUser->github) { ?>
              <div open="security:github" rounded pinline12 pblock8 fl gap jucsb alic hoverable>
                <div fl gap=smol+ alic>
                  <mi wide class="ri-github-fill"></mi>
                  <div>
                    <p text bold>GitHub</p>
                    <p text smol>Active &middot; <span color=company><?= Time::ago(CurrentUser->github->created_at, true); ?></span></p>
                  </div>
                </div>

                <mi midler>east</mi>
              </div>
            <?php } ?>
          <?php } ?>
        </div>
      </div>
    <?php endif;

    # Only show this section if the CurrentUser has any third party app open to con-
    # nect to.
    if (!CurrentUser->osu || !CurrentUser->discord || !CurrentUser->github) : ?>
      <div fl fldircol gap=smol+>
        <p text bold><?= __("Add connection") ?></p>

        <div fl gap=smol>
          <?php if (!CurrentUser->discord && Feature::enabled("connect_discord")) { ?>
            <mbutton mid has-icon=left filled
              data-action="connection:start"
              data-provider="discord"
              data-call-action="connect">
              <mi class="ri-discord-fill"></mi>
              <p text bold>Discord</p>
            </mbutton>
          <?php } ?>

          <?php if (!CurrentUser->github && Feature::enabled("connect_github")) { ?>
            <mbutton mid has-icon=left filled
              data-action="connection:start"
              data-provider="github"
              data-call-action="connect">
              <mi class="ri-github-fill"></mi>
              <p text bold>GitHub</p>
            </mbutton>
          <?php } ?>

          <?php if (!CurrentUser->osu && Feature::enabled("connect_osu")) { ?>
            <mbutton mid has-icon=left filled
              data-action="connection:start"
              data-provider="osu!"
              data-call-action="connect">
              <mi class="osu-icon osu-outlined"></mi>
              <p text bold>osu!</p>
            </mbutton>
          <?php } ?>
        </div>

        <p text slight>Clicking one of the options will open the authentication window of the third party service in the current browser tab.</p>
      </div>
    <?php endif; ?>
  </box-model>
</div>