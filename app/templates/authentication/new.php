<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Authentication;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$type = filter_var(get("type"), FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$redirect = filter_var(get("redirect"), FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Type not set?
 */
if (!$type)
  die($Request->error());

/**
 * Just get the latest authentication of the current user.
 * @var ?Authentication
 */
$Authentication =
  CurrentUser
  ->authentications()
  ->where("type", $type)
  ->whereNull("deleted_at")
  ->latest()
  ->first();

/**
 * Authentication doesn't exist?
 */
if (!$Authentication)
  die($Request->error());

/**
 * Begin the outpuff buffer.
 */
ob_start();

?>

<form
  request="<?= $Authentication->type ?>"
  responder
  <?= $redirect ? "redirect='$redirect'" : "reload" ?>
  <?= get("update-user-references") ? "update-user-references" : "" ?>>
  <content smolest prompt-height>
    <box-model prompt elevated rounded="wide" filled=lighter>
      <div prompt-content>
        <div prompt-header>
          <mi>fingerprint</mi>
          <div>
            <p title><?= $Authentication->display_type() ?></p>
            <p text>Validate your identity</p>
          </div>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <p text std mt tac>We've sent a mail to <strong><?= CurrentUser->email; ?></strong> with an authentication code.</p>

          <div input material has-icon>
            <i color=company>heia &nbsp;-</i>
            <input type="text" <?= DEV ? "value=$Authentication->code" : ""; ?> maxlength="6" autofocus name="authentication_code" placeholder="code" autocomplete="false" tabindex=1 />
          </div>

          <tipp-box outlined=darker rounded=mid>
            <mi>info</mi>
            <p text>If you haven't received the mail after 30 minutes, please cancel the request and create a new one.</p>
          </tipp-box>

          <div dynamic-color class="dot-container" mt mb>
            <div class="dot-pulse"></div>
            <div class="dot-pulse"></div>
            <div class="dot-pulse"></div>
          </div>

          <input type=hidden name=authentication_type value=<?= $type; ?> />
          <input type=hidden name=authentication_token value=<?= $Authentication->token; ?> />
          <?php if ($Authentication->value) : ?>
            <input type=hidden name=authentication_value value=<?= $Authentication->value; ?> />
          <?php endif; ?>
        </div>
      </div>

      <div prompt-actions>
        <mbutton material close-overlay>
          <p text>Cancel</p>
        </mbutton>
        <mbutton background=slight has-icon=left material submit-closest size=mid>
          <mi>done_all</mi>
          <p text bold>Done</p>
        </mbutton>
      </div>
    </box-model>
    </div>
</form>

<?php

die($Request->success(data: ob_get_clean()));
