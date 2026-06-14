<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\User;

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var string
 */
$gumode = filter_input(INPUT_GET, "gumode", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var int
 */
$fetch_limit = 7;

/**
 * Validate gumode.
 */
if (!in_array($gumode, Gamemode::$modes))
  $gumode = 0;

/**
 * @var object
 */
$mode_mod = Gamemode::convert_gumode_to_mode_mod($gumode);

/**
 * @var ?User
 */
$User = User::find($id);

/**
 * @var bool
 */
$is_my_profile = $User->is(CurrentUser);

/**
 * User doesn't exist?
 * ! Error
 */
if (!$User) :
  include GET_CONTENT_NOTHING;
else :

  /**
   * @var ?Beatmap
   */
  $Beatmaps = $User->most_played_beatmaps($gumode, $fetch_limit);

  /**
   * No beatmaps found?
   */
  if (!$Beatmaps->count()) {

?>

    <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap>
      <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
        <mi wide>web_stories</mi>
      </div>
      <div tac>
        <p text bold wide>Beatmaps</p>
        <p text std><?= __("Played beatmaps will be shown here") ?></p>
      </div>

      <?php if ($is_my_profile) : ?>
        <div fl jucc>
          <a href="/beatmaps?mode=<?= $gumode; ?>">
            <mbutton mid has-icon="right" background="dynamic">
              <p text bold><?= __("Explore beatmaps") ?></p>
              <mi>arrow_forward</mi>
            </mbutton>
          </a>
        </div>
      <?php endif; ?>
    </box-model>

  <?php } else { ?>

    <div class="beatmaps" style=padding-top:0;>
      <div class="beatmaps_content" grid-repeat gap=smol reset-width>
        <?php

        foreach ($Beatmaps as $key => $Beatmap) {
          if ($key == ($fetch_limit - 1)) break;

          echo "<div grid-keeper>";
          include COMPONENT . "/beatmaps/_beatmap.php";
          echo "</div>";
        }

        ?>
      </div>
    </div>

    <?php if ($Beatmaps->count() > ($fetch_limit - 1)) { ?>
      <div fl jucc mt=smol>
        <a href="<?= "/u/$User->id/$mode_mod->mode/$mode_mod->mod/beatmaps"; ?>">
          <mbutton ripple-effect filled=lighter has-icon=right>
            <p text smol bold ttup><?= __("Show more") ?></p>
            <mi>east</mi>
          </mbutton>
        </a>
      </div>
    <?php } ?>

<?php }

endif;
