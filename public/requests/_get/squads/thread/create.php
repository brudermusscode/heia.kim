<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Model\Score;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id   = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * User verified?
 * ! Error
 */
if (!VERIFIED)
  exit($Request->error("!UNVERIFIED"));

/**
 * Squad exists?
 * ! Error
 */
if (!$CurrentUser->squad)
  exit($Request->error("<strong>You are not in a Squad.</strong>"));

/**
 * @var Squad
 */
$Squad = $CurrentUser->squad;

/**
 * @var ?Score|Beatmap
 */
$Attachment = match ($type) {
  "score" => Score::find($id),
  "beatmap" => Set::find($id),
  default => null,
};

/**
 * Begin output buffer
 */
ob_start();

?>

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

?>

<form data-form="threads:create">
  <div content-width=smolest prompt-height>

    <box-model prompt filled=lighter elevated rounded=wide>
      <div prompt-content>
        <div prompt-header>
          <mi>gesture</mi>
          <p title><?= __("Create new thread") ?></p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <a href="<?= $Squad->link(); ?>">
            <box-model outlined="darker" rounded="mid" clickable>
              <bm-inr size="std">
                <div fl alic jucsb>
                  <div fl alic gap>
                    <picture size="midler" circled>
                      <?php $Squad->logo(); ?>
                    </picture>

                    <div fl fldircol gap="smoler">
                      <div fl alic gap="smol">
                        <div filled="darker" rounded="wide" pblock8 pinline4 ttup>
                          <p text smol bold color="dynamic"><?= $Squad->tag; ?></p>
                        </div>
                        <p text std bold><?= $Squad->name; ?></p>
                      </div>
                    </div>
                  </div>
                  <mi midler>east</mi>
                </div>
              </bm-inr>
            </box-model>
          </a>

          <div flexone>
            <div mb=smol fl gap=smol align-items=center>
              <p text bold><?= __("Title") ?></p>
            </div>

            <div input material>
              <input autofocus tabindex=1 type="text" name="title" placeholder="<?= __("Let's smash this beatmap"); ?>..." enter-submitable />
            </div>
          </div>

          <div fl fldircol gap=smol+>
            <div fl fldircol gap=smolest>
              <p text bold><?= __("First post") ?></p>
              <p text slight>
                <?= __("Tell the members of your squad in a little more detail what this thread should be about") ?>
              </p>
            </div>
            <div textarea>
              <textarea auto-resize material name="content" placeholder="<?= __("It's about"); ?>..." tabindex=2></textarea>
            </div>
          </div>

          <?php if ($CurrentUser->squad_user->can("manage", "content")) { ?>
            <div fl fldircol gap=smolest>
              <p text bold><?= __("Closed") ?></p>
              <div fl gap jucsb alistart>
                <div fl align-items=center gap>
                  <div style=line-height:1.2em;>
                    <p text slight><?= __("Check, if this thread should only be writable to owners") ?></p>
                  </div>
                </div>

                <toggle-switch toggled="false">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher"></div>
                    <input type="hidden" name="closed" value="0" />
                    <div fl fldirrow jucc>
                      <div fl fldirrow jucsb alic style="width:calc(100% - .8em);">
                      </div>
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>
          <?php } ?>

          <?php if ($Attachment) { ?>
            <div fl fldircol gap=smol>
              <p text std bold><?= __("Attachments") ?></p>
              <?php

              /**
               * SCORE
               */
              if ($Attachment instanceof Score) {
                $Score = $Attachment;
                $clean_appearance = true;

                echo <<<TEXT
                  <input type=hidden name="attachments[]" value="score/$Score->id" />
                TEXT;

                include TEMPLATE . "/score/_score.php";

                /**
                 * BEATMAP SET
                 */
              } else if ($Attachment instanceof Set) {
                $Set = $Attachment;

                echo <<<TEXT
                  <input type=hidden name="attachments[]" value="beatmap/$Set->id" />
                TEXT;

                include TEMPLATE . "/beatmapset/_set-card.php";
              }

              ?>
            </div>
          <?php } ?>
        </div>
      </div>

      <div prompt-actions>
        <mbutton close-overlay background=clean>
          <p text>Cancel</p>
        </mbutton>

        <div rounded=wide has-tooltip=bottom dno>
          <mbutton mid background=invert color=invert icon-only tabindex=4 disabled>
            <mi>attach_file_add</mi>
          </mbutton>
          <div ttooltip>
            <p text std bold><?= __("Add attachment") ?></p>
          </div>
        </div>

        <mbutton mid background=follow color=dark tabindex=3 submit-closest>
          <p text bold><?= __("Ok, create it") ?></p>
        </mbutton>
      </div>
    </box-model>

  </div>
</form>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
