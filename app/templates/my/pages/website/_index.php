<?php

use Bruder\Utils\Str;

?>

<box-model outlined flexone>
  <div p24>
    <div p12>
      <p text midler bold><?= __("Theme") ?></p>
      <p text std><?= __("Choose one of our ever growing selection of color themes") ?></p>
    </div>

    <a href="/my/website/theme">
      <div hoverable p12 rounded=mid fl gap justify-content=space-between align-items=center>
        <div fl gap align-items=center>
          <mi wide>smart_button</mi>
          <p text std><?= Str::format_theme_name(APP->get_current_theme()); ?></p>
        </div>
        <mi midler>east</mi>
      </div>
    </a>
  </div>
</box-model>

<div fl fldircol gap>
  <div title-inline>
    <p text mid bold>Behaviour</p>
    <p text std>Take all the settings you want about how the website behaves when you use it.</p>
  </div>

  <div fl gap=smol flex-wrap>
    <box-model outlined max-width-20-expand fl fldircol clickable toggle-animation=bump>
      <div p24 flexone jucsb fl fldircol>
        <div p12>
          <div fl fldircol gap=smol+>
            <mi wide>animation</mi>
            <p text midler bold><?= __("Animations"); ?></p>
          </div>
          <p text std><?= __("Toggle animations such as the particles at the login") ?></p>
        </div>

        <div p12 rounded=wide fl gap justify-content=space-between align-items=center>
          <p text std><?= __("Animations are") ?> </p>
          <div>
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
        </div>
      </div>
    </box-model>

    <box-model outlined max-width-20-expand fl fldircol clickable play-random-sound>
      <div p24 flexone jucsb fl fldircol>
        <div p12>
          <div fl fldircol gap=smol+>
            <mi wide>volume_up</mi>
            <p text midler bold>Sounds</p>
          </div>
          <p text std>Login, logout, failing a request or anything, that makes noise while using our website.</p>
        </div>

        <div p12 rounded=wide fl gap justify-content=space-between align-items=center>
          <p text std>Sounds are</p>
          <div>
            <toggle-switch mt=smol toggled=<?= SOUNDS_ENABLED ? "true" : "false"; ?> update-cookie-bool="SOUNDS">
              <div class="toggle_switch__inr">
                <div class="toggle_switch__switcher"></div>
                <div fl fldirrow justify-content="center">
                  <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                  </div>
                </div>
              </div>
            </toggle-switch>
          </div>
        </div>
      </div>
    </box-model>
  </div>
</div>