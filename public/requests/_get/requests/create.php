<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Application\Application;
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
$Beatmap = Beatmap::find($id);

/**
 * Beatmap exists?
 */
if (!$Beatmap)
  exit($Request->error());

/**
 * Create featured artists for this Beatmap.
 */
$Beatmap->create_featured_artists();

/**
 * Begin the outpuff buffer.
 */
ob_start();

?>

<div content-width=smoler prompt-height>
  <box-model prompt elevated filled=lighter>
    <form data-form="requests:create" responder>
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

          <tipp-box outlined=darker rounded=mid>
            <mi>info</mi>
            <p text>We will notify you about the ranking</p>
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
