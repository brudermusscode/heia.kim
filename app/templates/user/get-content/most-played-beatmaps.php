<?php

use Illuminate\Support\Collection;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Beatmap;
use Heiakim\Model\User;

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
$gumode = filter_input(INPUT_GET, "gumode", FILTER_VALIDATE_INT) ?? 0;
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

$is_my_profile = $User->is(CurrentUser);

if (!$User) : include GET_CONTENT_NOTHING;
else :

  /**
   * @var Collection<Beatmap>
   */
  $Beatmaps = $User->most_played_beatmaps($gumode, $fetch_limit);

  if (!$Beatmaps->count()) : ?>

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

  <?php else : ?>

    <div grid-repeat gap=smol>
      <?php foreach ($Beatmaps as $key => $Beatmap) :
        if ($key == ($fetch_limit - 1)) break;

        include TEMPLATE . "/beatmap/_beatmap-column.php";
      endforeach; ?>
    </div>

    <?php if ($Beatmaps->count() > ($fetch_limit - 1)) : ?>
      <div fl jucc mt=smol>
        <a href="<?= "/u/$User->id/beatmaps/$mode_mod->mode/$mode_mod->mod"; ?>">
          <mbutton ripple-effect filled=lighter has-icon=right>
            <p text smol bold ttup><?= __("Show more") ?></p>
            <mi>east</mi>
          </mbutton>
        </a>
      </div>
    <?php endif; ?>

<?php endif;
endif;
