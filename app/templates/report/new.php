<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Report;
use Heiakim\Model\User;

authorize(CurrentUser);

$id   = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

$Reference = Report::find_reference_or_die($type, $id);

/**
 * @var ?Report
 */
$Report = CurrentUser->reports()
  ->where("reference_id", $id)
  ->where("report_type", $type)
  ->first();

if ($Report)
  die(error("<strong>You have reported this already.</strong>"));

# Content belongs to the CurrentUser?
if (CurrentUser->is($Reference instanceof User ? $Reference : $Reference?->user))
  die(error("<strong>Why you want report urself? 😒</strong>"));

# Apply the reference to any possible variable of an object that the included HTML ob-
# ject might need.
$Score = $Comment = $User = $Post = $Reference;

ob_start();

include SNOW; ?>

<content smolplus>
  <form request="report:create" close-overlays responder>
    <box-model prompt elevated filled=lighter>
      <div prompt-content>
        <div prompt-header>
          <mi>campaign</mi>
          <p title><?= __("Report") ?></p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <div color=dynamic>
            <?php

            $clean_appearance = true;
            $hide_reactions = true;

            # + Model object as HTML.
            include TEMPLATE . (match ($type) {
              "score" => "/score/_score.php",
              "comment" => "/comment/_comment.php",
              "user" => "/user/_user_report.php",
              "squad:post" => "/squad/_post_report.php",
              "squad:post:comment" => "/squad/_post_comment_report.php",
              default => "/global/_invalid_request.php",
            });

            ?>
          </div>

          <div fl fldircol gap=smol>
            <p text bold std><?= __("Reason") ?></p>
            <div textarea>
              <textarea autofocus filled auto-resize material name="comment_string" placeholder="<?= __("It's because") ?>..."></textarea>
            </div>
          </div>

          <div fl fldircol gap=smolest>
            <p text bold std><?= __("Want to be notified?") ?></p>
            <div fl gap=mid alic>
              <p text std slight>
                <?= __("Receive notifications about possible outcomes and process of the report.") ?></p>
              <toggle-switch toggled="true">
                <div class="toggle_switch__inr">
                  <div class="toggle_switch__switcher" toggled="false"></div>
                  <input type="hidden" name="user_notification" value="1" />
                  <div fl fldirrow justify-content="center">
                    <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </div>
              </toggle-switch>
            </div>
          </div>
        </div>
      </div>

      <div prompt-actions fl jucsb>
        <mbutton close-overlay background=clean color=dynamic>
          <p text>Cancel</p>
        </mbutton>
        <mbutton mid background=besure color=dark-orange tabindex=3 submit-closest>
          <p text bold><?= __("Submit") ?></p>
        </mbutton>
      </div>
    </box-model>

    <input type="hidden" name="report_type" value="<?= $type; ?>" />
    <input type="hidden" name="reference_id" value="<?= $id; ?>" />
  </form>
</content>

<?php die(success(data: ob_get_clean()));
