<?php

use Heiakim\Time\Time;

?>

<div fl gap alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>
  <p text mid bold>Mailing</p>
</div>

<form request="user:setting:update" delay="20" radio responder=error no-loader fl fldircol gap=smol>
  <box-model p32 filled fl fldircol gap=smol+>
    <div fl jucsb gap=smol+ alic>
      <p text bold midler>Newsletter</p>
      <mbutton smol tag filled=lighter has-icon=left>
        <mi>event</mi>
        <p text smol>weekly</p>
      </mbutton>
    </div>
    <div fl jucsb gap>
      <div style=flex:1;>
        <p text>Our newsletter includes the top scores of the week from public players, new ranked/loved
          beatmaps,
          updates to our services and much more.</p>
      </div>

      <div>
        <toggle-switch submit-closest mt=smol toggled=<?= CurrentUser->privacy->mailing_newsletter ? "true" : "false"; ?>>
          <div class="toggle_switch__inr">
            <div class="toggle_switch__switcher"></div>
            <input type="hidden" name="mailing_newsletter" value="<?= CurrentUser->privacy->mailing_newsletter; ?>" />
            <div fl fldirrow justify-content="center">
              <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
              </div>
            </div>
          </div>
        </toggle-switch>
      </div>
    </div>
  </box-model>

  <box-model p32 filled fl fldircol gap=smol+>
    <div fl jucsb gap=smol+ alic>
      <p text bold midler>Play reminder</p>
      <mbutton smol tag filled=lighter has-icon=left>
        <mi>event</mi>
        <p text smol>monthly</p>
      </mbutton>
    </div>
    <div fl jucsb gap>
      <div style=flex:1;>
        <p text>We will remind you of our services after a certain amount of time without any activity and
          invite you to play again.
          These mails might include news about updates or public players.
        </p>
      </div>

      <div>
        <toggle-switch submit-closest mt=smol toggled=<?= CurrentUser->privacy->mailing_reminder ? "true" : "false"; ?>>
          <div class="toggle_switch__inr">
            <div class="toggle_switch__switcher"></div>
            <input type="hidden" name="mailing_reminder" value="<?= CurrentUser->privacy->mailing_reminder; ?>" />
            <div fl fldirrow justify-content="center">
              <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
              </div>
            </div>
          </div>
        </toggle-switch>
      </div>
    </div>
  </box-model>

  <box-model p32 filled fl fldircol gap=smol+>
    <div fl jucsb gap=smol+ alic>
      <p text bold midler>Birthday wishes</p>
      <mbutton smol tag filled=lighter has-icon=left>
        <mi>event</mi>
        <p text smol>yearly</p>
      </mbutton>
    </div>

    <div fl jucsb gap alistart>
      <div style=flex:1;>
        <p text>We will send you some wishes on your special day. This will only happen, when you have set your
          birthday in your <a data-category=personal curpo data-sub=birthday normal>Personal Settings</a>.
        </p>
      </div>

      <toggle-switch submit-closest mt=smol
        toggled=<?= CurrentUser->privacy->mailing_birthday ? "true" : "false"; ?>>
        <div class="toggle_switch__inr">
          <div class="toggle_switch__switcher"></div>
          <input type="hidden" name="mailing_birthday" value="<?= CurrentUser->privacy->mailing_birthday; ?>" />
          <div fl fldirrow justify-content="center">
            <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
            </div>
          </div>
        </div>
      </toggle-switch>
    </div>

    <div p12 rounded=std hoverable
      data-category=personal
      data-sub=birthday>
      <div fl gap alic jucsb>
        <div fl gap=smol+ alic>
          <mi mid>celebration</mi>
          <p text>
            <?= CurrentUser->settings->birthday ? date_format(date_create(CurrentUser->settings->birthday), 'd. F Y') : 'Set your birthday'; ?>
          </p>
        </div>
        <mi midler>arrow_forward</mi>
      </div>
    </div>
  </box-model>

  <box-model p32 filled fl fldircol gap=smol+>
    <div fl jucsb gap=smol+ alic>
      <p text bold midler><?= PREMIUM_NAME; ?> expiration reminder</p>
    </div>

    <div fl jucsb gap>
      <div style=flex:1;>
        <p text>We will remind you some days before enhanced experience of your <?= PREMIUM_NAME; ?>
          features will expire.
        </p>
      </div>

      <div>
        <toggle-switch submit-closest mt=smol toggled=<?= CurrentUser->privacy->mailing_expiring_premium ? "true" : "false"; ?>>
          <div class="toggle_switch__inr">
            <div class="toggle_switch__switcher"></div>
            <input type="hidden" name="mailing_expiring_premium" value="<?= CurrentUser->privacy->mailing_expiring_premium; ?>" />
            <div fl fldirrow justify-content="center">
              <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
              </div>
            </div>
          </div>
        </toggle-switch>
      </div>
    </div>

    <?php

    /**
     * @var ?string
     */
    $premium_time_left = Time::left(CurrentUser->donor_end);

    ?>

    <a href="/unlock/premium" sub>
      <div p12 rounded=std hoverable>
        <div fl gap alic jucsb>
          <div fl gap=smol+ alic>
            <mi mid><?= PREMIUM_ICON; ?></mi>
            <p text><?= $premium_time_left ?? "Unlock " . PREMIUM_NAME; ?></p>
          </div>
          <mi midler>arrow_forward</mi>
        </div>
      </div>
    </a>
  </box-model>
</form>

<?php include TEMPLATE . "/my/_autosave.php";
