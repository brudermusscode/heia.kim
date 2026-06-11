<?php

use Heiakim\Time\Time;

$premium_time_left = Time::left(CurrentUser->donor_end);

?>

<div fl fldircol gap=mid>

  <?php

  /**
   * USER RECOMMENDED SETTINGS
   */
  if (CurrentUser->has_recommended_settings()) { ?>
    <a href="/my/recommendations" sub>
      <box-model filled clickable>
        <bm-inr size=mid fl fldircol gap>
          <div fl gap jucsb alic>
            <div fl gap alic>
              <mi mid>error</mi>
              <div fl fldircol gap=smoler>
                <p text midler bold>Recommended Settings</p>
                <p text std>You have pending settings, we recommend for you to set and complete your account.</p>
              </div>
            </div>
            <mi midler>east</mi>
          </div>
        </bm-inr>
      </box-model>
    </a>
  <?php } ?>


  <box-model outlined=darker>
    <bm-inr size=std fl fldircol gap>
      <div fl alic gap=smol+>
        <div circled fl alic jucc style="height:3.2em;width:3.2em;" background=follow color=dark-green posrel>
          <div notification-dot></div>
          <mi>vpn_key</mi>
        </div>
        <p text mid bold><?= __("Security Settings") ?></p>
      </div>
      <div fl fldircol gap=smol>
        <p text std>
          <?= __("We have added a new security tab in your settings area, which gives you the option to secure your account from third party access and <strong>connect it with other apps</strong> to ease the use of our website.") ?>
        </p>
      </div>
      <div fl jucend pblock12>
        <mbutton data-category=security material filled>
          <p text bold><?= __("Manage settings") ?></p>
        </mbutton>
      </div>
    </bm-inr>
  </box-model>

  <box-model outlined=darker>
    <bm-inr size=std fl fldircol gap>
      <div fl alic gap=smol+>
        <div circled fl alic jucc style="height:3.2em;width:3.2em;" background=refollow color=dark-blue posrel>
          <mi>smart_button</mi>
        </div>
        <p text mid bold><?= __("Want a different look?") ?></p>
      </div>

      <div fl fldircol gap=smol>

        <p text std>
          <?= __("We have a whole bunch of themes to choose from which all come with a dark and light mode.") ?>
        </p>
      </div>
      <div fl jucend pblock12>
        <mbutton data-category=website data-sub=theme material filled>
          <p text bold><?= __("Explore themes") ?></p>
        </mbutton>
      </div>
    </bm-inr>
  </box-model>

  <?php if (!CurrentUser->donor_end || CurrentUser->donor_end < time()) { ?>
    <a href="/unlock/premium" grid-keeper>
      <box-model background=premium color=premium clickable>
        <bm-inr size=mid fl fldircol gap>
          <div fl gap jucsb alic>
            <div fl gap alic>
              <mi mid><?= PREMIUM_ICON; ?></mi>
              <div fl fldircol gap=smoler>
                <p text mid bold><?= PREMIUM_NAME; ?></p>
                <p text std><?= __("Unlock some cool features and enhance the uniqueness of your account!") ?>
                </p>
              </div>
            </div>
            <mi midler>east</mi>
          </div>
        </bm-inr>
      </box-model>
    </a>
  <?php } ?>
</div>

<divide horiz></divide>

<div fl fldircol gap=smol>
  <div fl alic gap=smol color=yellow>
    <mi>privacy_tip</mi>
    <p text bold>Privacy Tip</p>
  </div>
  <p text>
    <?= __("Your settings are only visible to you. In the most sections and settings pages, we have added privacy information which will inform you about their publicity. If you want to read more about what we collect, how we process and use your data, you can read along our {privacy-policy-link}.") ?>
  </p>
</div>