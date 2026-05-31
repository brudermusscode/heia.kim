<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var Request $Request
 * @var ?Squad $CurrentSquad
 */

authorize(resource: CurrentUser);

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var Squad
 */
$Squad = Squad::find($id);

/**
 * Squad not found?
 * ! Error
 */
if (!$Squad)
  exit($Request->error());

/**
 * User is member already?
 * ! Error
 */
if ($CurrentSquad)
  exit($Request->error("<strong>This is your squad!</strong>"));

/**
 * Begin the output buffer.
 */
ob_start(); ?>

<style>
  .stars {
    position: fixed;
    z-index: -1;
    height: 100vh;
    width: 100vw;
  }
</style>

<?php

if (ANIMATIONS_ENABLED) {
  echo '<div class="stars">';
  for ($i = 0; $i < 80; $i++)
    echo '<div class="snow"></div>';
  echo '</div>';
}

/**
 * @var string
 */
$join_action = !$Squad->is_public()
  ? "squad:request:create"
  : "squad:user:create";

?>

<div content-width=smoler prompt-height>
  <form request="<?= $join_action ?>" reload responder=always update-user-references>
    <input type=hidden name=id value=<?= $Squad->id; ?> />

    <?php

    /**
     * Only append a type of joining if the squad is not public,
     * since it will create a SquadRequest instead of a SquadUser.
     */
    if (!$Squad->is_public()) : ?>
      <input type=hidden name=type value=join />
    <?php endif; ?>

    <box-model prompt elevated rounded=wide>
      <div prompt-content>
        <div prompt-header>
          <mi><?= !$Squad->is_public() ? "arrow_circle_right" : "add_circle"; ?></mi>
          <p title><?= !$Squad->is_public() ? "Request to join" : "Join"; ?></p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <?php if (!$Squad->is_public()) { ?>
            <p text std>
              <?= str_replace("{contribution-factor}", SquadUser::$contribution_factor * 100, __("You are about to send a request to join this squad. When accepted, you will be notified and <strong>{contribution-factor} % of your total Performance Points</strong> will be added to the bank of each available gamemode of this squad.")) ?>
            </p>
          <?php } else { ?>
            <p text std>
              <?= str_replace("{contribution-factor}", SquadUser::$contribution_factor * 100, __("You are about to join this squad. A public feed entry and <strong>{contribution-factor} % of your total Performance Points</strong> will be added to the bank of each available gamemode of this squad.")) ?>
            </p>
          <?php } ?>

          <div fl fldircol gap=smol+>
            <p text std bold><?= __("Available gamemodes") ?> and what you will contribute</p>
            <div fl fldircol gap=smoler>
              <?php foreach ($Squad->modes as $mode => $active) {

                /**
                 * Skip disabled.
                 */
                if ($active == 0)
                  continue;

                /**
                 * @var array
                 */
                $gumodes = Gamemode::$mods_int_per_mode[$mode];

                $Stats = CurrentUser->stats;

              ?>
                <div outlined=darker rounded=mid pblock12 pinline24 fl align-items="center" gap="std">
                  <p text mid>
                    <i class="osu-icon osu-<?= $mode == "osu" ? "vanilla" : $mode; ?>"></i>
                  </p>
                  <div fl fldircol gap="smoler">
                    <p text std bold><?= Gamemode::convert_mode_to_full_name($mode); ?></p>
                    <div fl style="gap:.2em;">

                      <?php

                      $gumode = Gamemode::get_gumode_as_int($mode, "vanilla");

                      $contributing = number_format(
                        $Stats->filter(function ($q) use ($gumode) {
                          return $q->mode == $gumode;
                        })
                          ->values()
                          ->first()
                          ->pp * SquadUser::$contribution_factor
                      );

                      ?>
                      <div size="smol" rounded="wide" background="invert">
                        <div pinline12 pblock4>
                          <p text std color="invert">STD <strong color=company><?= $contributing; ?>pp</strong></p>
                        </div>
                      </div>

                      <?php

                      if (in_array($mode, ["osu", "ctb", "taiko"])) {

                        $gumode = Gamemode::get_gumode_as_int($mode, "relax");

                        $contributing = number_format(
                          $Stats->filter(function ($q) use ($gumode) {
                            return $q->mode == $gumode;
                          })
                            ->values()
                            ->first()
                            ->pp * SquadUser::$contribution_factor
                        );

                      ?>
                        <div size="smol" rounded="wide" background="invert">
                          <div pinline12 pblock4>
                            <p text std color="invert">RX <strong color=company><?= $contributing; ?>pp</strong></p>
                          </div>
                        </div>
                      <?php } ?>

                      <?php

                      if (in_array($mode, ["osu"])) {

                        $gumode = Gamemode::get_gumode_as_int($mode, "autopilot");

                        $contributing = number_format(
                          $Stats->filter(function ($q) use ($gumode) {
                            return $q->mode == $gumode;
                          })
                            ->values()
                            ->first()
                            ->pp * SquadUser::$contribution_factor
                        );


                      ?>
                        <div size="smol" rounded="wide" background="invert">
                          <div pinline12 pblock4>
                            <p text std color="invert">AP <strong color=company><?= $contributing; ?>pp</strong></p>
                          </div>
                        </div>
                      <?php } ?>

                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>

          <div fl fldircol gap=smol+>
            <p text std bold><?= __("What you can do") ?></p>
            <div fl fldircol gap=smoler>
              <div outlined=darker rounded=mid pblock12 pinline24 fl gap=smol+ alic>
                <mi>post_add</mi>
                <p text std>Post & comment in feed</p>
              </div>
              <div outlined=darker rounded=mid pblock12 pinline24 fl gap=smol+ alic>
                <mi>gesture</mi>
                <p text std><?= __("Discuss Beatmaps & Scores in Threads") ?></p>
              </div>
              <div outlined=darker rounded=mid pblock12 pinline24 fl gap=smol+ alic>
                <mi>data_exploration</mi>
                <p text std><?= __("Score together in a team") ?></p>
              </div>
              <div outlined=darker rounded=mid pblock12 pinline24 fl gap=smol+ alic>
                <mi>arrow_warm_up</mi>
                <p text std>Work your way up to higher privileges to help manage this squad</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div prompt-actions>
        <mbutton close-overlay material background=clean color=dynamic>
          <p text>Cancel</p>
        </mbutton>
        <?php if (!$Squad->is_public()) { ?>
          <mbutton size=mid background=besure material submit-closest>
            <p text std bold><?= __("Let's go!") ?></p>
          </mbutton>
        <?php } else { ?>
          <mbutton size=mid background=company color=light material submit-closest>
            <p text std bold><?= __("Yes, let's go!") ?></p>
          </mbutton>
        <?php } ?>
      </div>
    </box-model>
  </form>
</div>

<?php

die($Request->success(data: ob_get_clean()));
