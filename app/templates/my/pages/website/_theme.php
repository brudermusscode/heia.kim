<?php

use Heiakim\Model\Gamemode;
use Heiakim\Utils\Str;

$preferred_gamemode = CurrentUser->preferred_mode;

/**
 * @var object
 */
$gumode_text = Gamemode::gumode_text($preferred_gamemode);

?>

<div content-width=smol>
  <div mt=wide mb fl gap=mid align-items="center" mb=std>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>

    <label size="mid" has-secondary>
      <div class="label__main">
        <p bold><?= __("Theme") ?></p>
      </div>
    </label>
  </div>

  <div fl fldircol gap>
    <div chooser fl fldircol gap=smol>
      <?php foreach (APP->get_all_themes() as $theme) { ?>
        <div
          data-action="my:website,theme"
          chooser-option="<?= $theme; ?>"
          class=color-theme rounded=mid outlined=darker p32 <?php if (APP->get_current_theme() == $theme) echo "active"; ?>>
          <div fl gap=smol justify-content=space-between align-items=center>
            <div fl fldircol gap=smol style=width:180px;>
              <p text std bold><?= Str::format_theme_name($theme); ?></p>
              <div show-active active-flex gap=smol align-items=center>
                <div fl justify-content=center align-items=center background=green
                  style=border-radius:50%;height:1.4em;width:1.4em;>
                  <p text std color=white>
                    <i class="mi">check</i>
                  </p>
                </div>
                <p text std><?= __("Selected") ?></p>
              </div>
            </div>
            <div fl gap flex-wrap=wrap justify-content=end>
              <div>
                <p mb=smol text smol timestamp bold>LIGHT</p>
                <div class="theme-accents <?= $theme; ?>">
                  <div class=main></div>
                  <div class=sub></div>
                  <div class=bg></div>
                  <div class=text></div>
                </div>
              </div>
              <div>
                <p mb=smol text smol timestamp bold>DARK</p>
                <div class="theme-accents <?= $theme; ?>-dark">
                  <div class=main></div>
                  <div class=sub></div>
                  <div class=bg></div>
                  <div class=text></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
      <input type=hidden chooser-input value="<?= APP->get_current_theme(); ?>" />
    </div>
  </div>
</div>