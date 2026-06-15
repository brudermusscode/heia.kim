<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\User;
use Illuminate\Support\Collection;

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var int
 */
$gumode = filter_input(INPUT_GET, "gumode", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var int
 */
$fetch_limit = 7;

# Validate gumode.
if (!in_array($gumode, Gamemode::$modes))
  $gumode = 0;

/**
 * @var object
 */
$mode_mod = Gamemode::gumode_text($gumode);

/**
 * @var ?User
 */
$User = User::find($id);

# User doesn't exist?
if (!$User) :
  include GET_CONTENT_NOTHING;
else :

  /**
   * @var Collection<Score>
   */
  $Scores = $User->first_place_scores(
    gumode: $gumode,
    order: "pp",
    limit: $fetch_limit,
  );

  if (!$Scores->count()) : ?>

    <box-model rounded="wide" filled="lighter" p42 fl fldircol alic gap style="flex:1;">
      <div style="height:3.2em;width:3.2em;" fl alic jucc circled filled>
        <mi mid>emoji_events</mi>
      </div>
      <div tac>
        <p text bold mid>First places</p>
        <p text std>Scores that rank first in the leaderboard</p>
      </div>
    </box-model>

  <?php else : ?>

    <div grid-repeat gap=smol>
      <?php

      # + Include all Scores.
      foreach ($Scores as $key => $Score) :
        if ($key == ($fetch_limit - 1)) break;

        include TEMPLATE . "/score/_score.php";
      endforeach; ?>
    </div>

    <?php if ($Scores->count() > ($fetch_limit - 1)) : ?>
      <div fl jucc mt=smol>
        <a href="<?= "/u/$User->id/performances-first/$mode_mod->mode/$mode_mod->mod"; ?>">
          <mbutton ripple-effect filled=lighter has-icon=right>
            <p text smol bold ttup><?= __("Show more") ?></p>
            <mi>east</mi>
          </mbutton>
        </a>
      </div>
    <?php endif; ?>

<?php unset($Scores);
  endif;
endif;
