<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Beatmap;

authorize(CurrentUser);

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var ?Beatmap
 */
$Beatmap = Beatmap::findOrReturn($id);

$Beatmap->create_featured_artists();

ob_start();

include SNOW; ?>

<content std>
  <box-model prompt elevated filled=lighter>
    <form request="request:create" reload responder>
      <input type="hidden" name="map_id" value="<?= $id; ?>" />

      <div prompt-content>
        <div prompt-header>
          <mi>forward</mi>
          <p title>Request Ranking</p>
        </div>

        <div prompt-inner-content fl fldircol alistart gap maxw100>
          <div class=notif__score style="margin-bottom:-2.4em;" w100 flone>
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

          <mbutton has-icon=left z maxw100
            beatmap-difficulty-bg="<?= $Beatmap->turn_difficulty_to_text(); ?>">
            <mi size=std>star</mi>
            <p text><?= number_format($Beatmap->diff, 2, ",", "."); ?></p>
            <p text bold trimt><?= $Beatmap->version; ?></p>
          </mbutton>

          <tipp-box outlined rounded fl alic>
            <mi>info</mi>
            <p text>We will notify you about a changes to this beatmap.</p>
          </tipp-box>
        </div>
      </div>

      <div prompt-actions fl jucsb>
        <mbutton close-overlay background=clean>
          <p text>Cancel</p>
        </mbutton>
        <mbutton mid background=besure color=dark-orange tabindex=3 submit-closest>
          <p text bold><?= __("Submit") ?></p>
        </mbutton>
      </div>
    </form>
  </box-model>
</content>

<?php die(success(data: ob_get_clean()));
