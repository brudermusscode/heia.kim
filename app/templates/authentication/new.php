<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Authentication;

$type = aglobal("type");
$redirect = filter_var(get("redirect"), FILTER_SANITIZE_SPECIAL_CHARS);

if (!$type)
  die(error());

/**
 * Just get the latest authentication of the current user.
 * @var ?Authentication
 */
$Authentication = CurrentUser->authentications()
  ->where("type", $type)
  ->whereNull("deleted_at")
  ->latest()
  ->first();

if (!$Authentication)
  die(error());

ob_start(); ?>
<form responder
  request="<?= $Authentication->type ?>"
  <?= $redirect ? "redirect='$redirect'" : "reload" ?>
  <?= get("update-user-references") ? "update-user-references" : "" ?>>
  <content std prompt-height minlineauto>
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
        <mbutton mid background=slight has-icon=left submit-closest>
          <mi>done_all</mi>
          <p text bold>Done</p>
        </mbutton>
      </div>
    </box-model>
  </content>
</form>

<?php die(success(data: ob_get_clean()));
