<?php

use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Score;

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
 * User doesn't exist?
 * ! Error
 */
if (!$User) :
  include GET_CONTENT_NOTHING;
else :

  /**
   * @var Score
   */
  $Scores = $User->scores()
    ->with("beatmap")
    ->where("mode", $gumode)
    ->orderBy("play_time", "DESC")
    ->limit($fetch_limit)
    ->get();

  /**
   * No scores found?
   */
  if (!$Scores->count()) {

?>

    <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap>
      <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
        <mi wide>browse_gallery</mi>
      </div>
      <div tac>
        <p text bold wide><?= __("Recently played") ?></p>
        <p text std><?= __("Scores, that were made recently will be shown here") ?></p>
      </div>
    </box-model>

  <?php } else { ?>

    <div grid-repeat gap=smol>
      <?php

      foreach ($Scores as $key => $Score) {
        if ($key == ($fetch_limit - 1)) break;

        include TEMPLATE . "/score/_score.php";
      }

      ?>
    </div>

    <?php if ($Scores->count() > ($fetch_limit - 1)) { ?>
      <div fl jucc mt=smol>
        <a href="<?= "/u/$User->id/$mode_mod->mode/$mode_mod->mod/recent"; ?>">
          <mbutton material ripple-effect filled=lighter has-icon=right>
            <p text smol bold ttup><?= __("Show more") ?></p>
            <mi>east</mi>
          </mbutton>
        </a>
      </div>
    <?php } ?>

<?php }

endif;
