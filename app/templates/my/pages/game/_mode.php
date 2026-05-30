<?php

use Bruder\Heiakim\Model\Gamemode;

$preferred_gamemode = $CurrentUser->preferred_mode;

/**
 * @var object
 */
$gumode_text = Gamemode::get_gumode_as_text($preferred_gamemode);

?>

<div fl gap=mid alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>

  <label size="mid" has-secondary>
    <div class="label__main">
      <p bold><?= __("Favorite mode") ?></p>
    </div>
  </label>
</div>

<form request="user:update" delay="200" radio responder=error>
  <div fl fldircol gap=mid>

    <setter <?= "mode=" . $gumode_text->mode; ?>>
      <div fl gap=mid fldircol>
        <div fl fldircol gap=smol+>
          <p text midler bold title-inline>Gamemode</p>

          <radio size fl gap=smol justify-content=center mode>
            <div flexone class="r__option" data-value="osu" submit-closest <?php if ($gumode_text->mode == "osu") echo "active"; ?>>
              <div fl fldircol alistart gap=smol+>
                <i text wide class="osu-icon osu-vanilla"></i>
                <p text no-word-wrap>Standart</p>
              </div>
            </div>

            <div flexone class="r__option" data-value="taiko" submit-closest <?php if ($gumode_text->mode == "taiko") echo "active"; ?>>
              <div fl fldircol alistart gap=smol+>
                <i text wide class="osu-icon osu-taiko"></i>
                <p text no-word-wrap>Taiko</p>
              </div>
            </div>

            <div flexone class="r__option" data-value="ctb" submit-closest <?php if ($gumode_text->mode == "ctb") echo "active"; ?>>
              <div fl fldircol alistart gap=smol+>
                <i text wide class="osu-icon osu-ctb"></i>
                <p text no-word-wrap>Catch the Beat</p>
              </div>
            </div>

            <div flexone class="r__option" data-value="mania" submit-closest <?php if ($gumode_text->mode == "mania") echo "active"; ?>>
              <div fl fldircol alistart gap=smol+>
                <i text wide class="osu-icon osu-mania"></i>
                <p text no-word-wrap>Mania</p>
              </div>
            </div>

            <input type="hidden" name=mode value=<?= $gumode_text->mode; ?> />
          </radio>
        </div>

        <div fl fldircol gap=smol+>
          <p text midler bold title-inline>Mod</p>

          <radio size fl gap=smol jucstretch mod>
            <div mod=vanilla flexone class="r__option" data-value="vanilla" submit-closest <?php if ($gumode_text->mod == "vanilla") echo "active"; ?>>
              <p text std>Vanilla</p>
            </div>

            <div mod=relax flexone class="r__option" data-value="relax" submit-closest <?php if ($gumode_text->mod == "relax") echo "active"; ?>>
              <p text std>Relax</p>
            </div>

            <div mod=autopilot flexone class="r__option" data-value="autopilot" submit-closest <?php if ($gumode_text->mod == "autopilot") echo "active"; ?>>
              <p text std>Autopilot</p>
            </div>

            <input type="hidden" name=mod value=<?= $gumode_text->mod; ?> />
          </radio>
        </div>
      </div>
    </setter>
  </div>
</form>

<?php include TEMPLATE . "/my/_autosave.php";
