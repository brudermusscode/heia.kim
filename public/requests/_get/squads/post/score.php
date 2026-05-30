<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Heiakim\Model\Score;

$Request = new Request;

/**
 * GET parameter.
 */
$id   = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * User verified?
 */
if (!VERIFIED)
  exit($Request->NO($Request->dd->NOT_VERIFIED));

/**
 * User has squad?
 */
if (!$CurrentUser->squad)
  exit($Request->NO($Request->dd->NOT_VERIFIED));

/**
 * @var ?Score
 */
$Score = Score::find($id);
if (!$Score)
  exit($Request->NO("<strong>Where did it go?</strong>"));

/**
 * Begin the outpuff buffer.
 */
ob_start();

?>

<div content-width=smoler prompt-height>
  <box-model prompt elevated filled=lighter>
    <form request-do="squad/post/create" responder=always>
      <input type="hidden" name="type" value=score />
      <input type="hidden" name="reference_id" value="<?= $id; ?>" />

      <div prompt-content>
        <div prompt-header>
          <mi>prompt_suggestion</mi>
          <p title>Create Post</p>
        </div>

        <div prompt-inner-content fl fldircol gap>

          <?php

          $clean_appearance = true;
          $hide_reactions = true;

          /**
           * Include the corresponding HTML object.
           */
          include TEMPLATE . "/score/_score.php";

          ?>

          <div fl fldircol gap=smol+>
            <div fl fldircol gap=smolest>
              <p text bold std>Wanna say something to it?</p>
            </div>
            <textarea clean-wide autofocus auto-resize name=comment_string placeholder="Look at me!"></textarea>
          </div>

          <div fl fldircol gap=smolest>
            <p text bold std>Enable comments</p>
            <div fl gap=mid alic>
              <p text std slight>When enabled, other squad members can comment your post.</p>
              <toggle-switch toggled="true">
                <div class="toggle_switch__inr">
                  <div class="toggle_switch__switcher" toggled="true"></div>
                  <input type="hidden" name=enable_comments value="1" />
                  <div fl fldirrow justify-content="center">
                    <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </div>
              </toggle-switch>
            </div>
          </div>

          <tipp-box outlined=darker rounded=mid>
            <mi>privacy_tip</mi>
            <p text>Votes will always be enabled for any post you create.</p>
          </tipp-box>
        </div>
      </div>
      <div prompt-actions fl jucsb>
        <mbutton close-overlay material background=clean color=dynamic>
          <p text>Cancel</p>
        </mbutton>
        <mbutton size=mid filled material submit-closest>
          <p text bold><?= __("Submit") ?></p>
        </mbutton>
      </div>
    </form>
  </box-model>
</div>

<?php

exit($Request->YES(data: ob_get_clean()));
