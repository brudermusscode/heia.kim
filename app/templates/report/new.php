<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Model\Report;
use Bruder\Heiakim\Model\User;

authorize(resource: $CurrentUser);

$id   = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Type is set?
 */
if (!$type || !in_array($type, Report::$types))
  request_error("<strong>You can't report this at the moment.</strong>", die: true);

/**
 * Find the Reference object.
 */
$Reference = Report::find_reference_or_die($type, $id);

/**
 * @var ?Report
 */
$Report = $CurrentUser
  ->reports()
  ->where("reference_id", $id)
  ->where("report_type", $type)
  ->first();

/**
 * Report already exists?
 */
if ($Report)
  exit($Request->error("<strong>Chill, you have reported this already.</strong>"));

/**
 * Content belongs to the action taking user?
 */
if ($CurrentUser->is($Reference instanceof User ? $Reference : $Reference?->user))
  request_error("<strong>Why you want report urself? 😒</strong>", die: true);

/**
 * Apply the reference to any possible variable of an object that
 * the included HTML object might need.
 */
$Score = $Comment = $User = $Post = $Reference;

ob_start();

include SNOW; ?>

<div content-width=smoler prompt-height>
  <box-model prompt elevated filled=lighter>
    <form request="report:create" close-overlays responder>
      <input type="hidden" name="report_type" value="<?= $type; ?>" />
      <input type="hidden" name="reference_id" value="<?= $id; ?>" />

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

            /**
             * Include the corresponding HTML object.
             */
            include TEMPLATE . (match ($type) {
              Report::$types[0] => "/score/_score.php",
              Report::$types[1] => "/comment/_comment.php",
              Report::$types[2] => "/user/_user_report.php",
              Report::$types[3] => "/squad/_post_report.php",
              Report::$types[4] => "/squad/_post_comment_report.php",
              default => "/global/_invalid_request.php",
            });

            ?>
          </div>

          <div fl fldircol gap=smol+>
            <div fl fldircol gap=smolest>
              <p text bold std><?= __("Reason") ?></p>
              <p text std slight><?= __("Why do you think this should be reported?") ?></p>
            </div>
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

<?php request_success(data: ob_get_clean(), die: true);
