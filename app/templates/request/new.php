<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Model\Beatmap;
use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id   = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * User verified?
 */
if (!VERIFIED)
  exit($Request->error("!UNVERIFIED"));

/**
 * @var ?Beatmap
 */
$Beatmap = Beatmap::findOrReturn($id, "<strong>WHAT? NOT FOUND MAN. 🫠</strong>");

/**
 * Create featured artists for this Beatmap.
 */
$Beatmap->create_featured_artists();

/**
 * Begin the outpuff buffer.
 */
ob_start();

include SNOW; ?>

<div content-width=smolest prompt-height>
  <box-model prompt elevated filled=lighter>
    <form request="request:create" reload responder>
      <input type="hidden" name="map_id" value="<?= $id; ?>" />

      <div prompt-content>
        <div prompt-header>
          <mi>forward</mi>
          <p title>Request Ranking</p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <div fl fldircol gap=smol>
            <div class=notif__score>
              <div class="cover">
                <picture>
                  <?php $Beatmap->set->cover(); ?>
                </picture>
              </div>
              <div class="notif__score_inr tac" p24>
                <p text bold mid trimt><?= $Beatmap->stripped_title(); ?></p>
                <p text trimt><?= $Beatmap->set->artists->first()->name; ?></p>
              </div>
            </div>

            <div fl alic jucstart gap=smol+>
              <mbutton material has-icon=left beatmap-difficulty-bg="<?= $Beatmap->turn_difficulty_to_text(); ?>">
                <mi size=std>star</mi>
                <p text><?= number_format($Beatmap->diff, 2, ",", "."); ?></p>
              </mbutton>
              <p text bold><?= $Beatmap->version; ?></p>
            </div>
          </div>

          <div fl fldircol>
            <p text bold>What will happen</p>
            <p text>Request a ranking will put this map in a list that our beloved beatmap nominators are checking regularly. They have full might and will decide about a state change.</p>
          </div>

          <tipp-box outlined=darker rounded=mid alistart>
            <mi>info</mi>
            <p text>We will notify you about a possible changed state of this map.</p>
          </tipp-box>
        </div>
      </div>

      <div prompt-actions fl jucsb>
        <mbutton close-overlay material background=clean color=dynamic>
          <p text>Cancel</p>
        </mbutton>
        <mbutton size=mid background=besure color=dark-orange material tabindex=3 submit-closest>
          <p text bold><?= __("Submit") ?></p>
        </mbutton>
      </div>
    </form>
  </box-model>
</div>

<?php

die($Request->success(data: ob_get_clean()));
