<?php

use Heiakim\Utils\Str;

?>

<box-model outlined flexone>
  <div p24>
    <div p12>
      <p text midler bold><?= __("Theme") ?></p>
      <p text std>
        <?= __("Choose one of our ever growing selection of color themes") ?></p>
    </div>

    <div open="website:theme" hoverable p12 rounded=mid fl gap justify-content=space-between align-items=center>
      <div fl gap align-items=center>
        <mi wide>smart_button</mi>
        <p text bold color=company>
          <?= Str::format_theme_name(APP->get_current_theme()); ?></p>
      </div>
      <mi midler>east</mi>
    </div>
  </div>
</box-model>

<div fl fldircol gap=smol+>
  <p text mid bold title-inline>Behaviour</p>

  <div fl gap=smol>
    <box-model flone outlined max-width-20-expand fl fldircol gap=smol clickable toggle-animation=bump p24>
      <div fl alic jucsb mb12>
        <mi wide>animation</mi>
        <toggle-switch mt=smol toggled=<?= ANIMATIONS_ENABLED ? "true" : "false"; ?> update-cookie-bool="ANIMATIONS">
          <div class="toggle_switch__inr">
            <div class="toggle_switch__switcher"></div>
            <div fl fldirrow justify-content="center">
              <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
              </div>
            </div>
          </div>
        </toggle-switch>
      </div>
      <p text midler bold><?= __("Animations"); ?></p>
      <p text std>
        <?= __("Toggle animations such as the particles at the login") ?></p>
    </box-model>

    <box-model flone outlined max-width-20-expand fl fldircol gap=smol clickable play-random-sound p24>
      <div fl alic jucsb mb12>
        <mi wide>volume_up</mi>
        <toggle-switch mt=smol
          toggled=<?= SOUNDS_ENABLED ? "true" : "false"; ?>
          update-cookie-bool="SOUNDS">
          <div class="toggle_switch__inr">
            <div class="toggle_switch__switcher"></div>
            <div fl fldirrow justify-content="center">
              <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
              </div>
            </div>
          </div>
        </toggle-switch>
      </div>
      <p text midler bold>Sounds</p>
      <p text std>Login, logout, failing a request or anything, that makes noise while using our website.</p>
    </box-model>
  </div>
</div>