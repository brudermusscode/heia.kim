<?php

use Heiakim\Time\Time;

$premium_time_left = Time::left(CurrentUser->donor_end);

?>

<?php if (CurrentUser->donor_end || CurrentUser->donor_end < time()) { ?>
  <box-model request-get="user:buy-premium" background=premium color=premium
    clickable>
    <bm-inr size=mid fl fldircol gap>
      <div fl gap jucsb alic>
        <div fl gap alic>
          <mi wide minline2><?= PREMIUM_ICON; ?></mi>
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
<?php } ?>

<?php if (CurrentUser->has_recommended_settings()) : ?>
  <a href="/my/recommendations" sub dno>
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
<?php endif; ?>


<box-model outlined=darker fl fldircol gap p32>
  <div fl alic gap=smol+>
    <div circled fl alic jucc style="height:52px;width:52px;" background=follow color=dark-green posrel>
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
    <mbutton open="security" filled>
      <p text bold><?= __("Manage settings") ?></p>
    </mbutton>
  </div>
</box-model>

<box-model outlined=darker fl fldircol gap p32>
  <div fl alic gap=smol+>
    <mi circled fl alic jucc style="height:52px;width:52px;" background=refollow color=dark-blue posrel>smart_button</mi>
    <p text mid bold><?= __("Want a different look?") ?></p>
  </div>

  <div fl fldircol gap=smol>
    <p text std>
      <?= __("We have a whole bunch of themes to choose from which all come with a dark and light mode.") ?>
    </p>
  </div>
  <div fl jucend pblock12>
    <mbutton open="website:theme" filled>
      <p text bold><?= __("Explore themes") ?></p>
    </mbutton>
  </div>
</box-model>

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