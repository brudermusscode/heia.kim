<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Authentication;

$type = aglobal("type") ?? die(error());
$attributes = [
  "redirect" => filter_var(get("redirect"), FILTER_SANITIZE_SPECIAL_CHARS),
  "full-redirect" => filter_var(get("full-redirect"), FILTER_SANITIZE_SPECIAL_CHARS),
  "um-open" => filter_var(get("um-open"), FILTER_SANITIZE_SPECIAL_CHARS),
  "reload" => filter_var(get("reload"), FILTER_SANITIZE_SPECIAL_CHARS),
  "full-reload" => filter_var(get("full-reload"), FILTER_SANITIZE_SPECIAL_CHARS),
];

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

<form request="authentication:update" responder close-overlay
  <?= get("update-user-references") ? "update-user-references" : "" ?>
  <?php foreach ($attributes as $attr => $val) :
    if (!$val) continue;
    echo "$attr='$val'";
  endforeach; ?>>
  <div style="min-height:100vh;" fl alic jucc pblock62>
    <content smolplus>
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
            <p text std mt tac>📬 &nbsp; Mail with a code sent to
              <strong color=company><?= CurrentUser->email; ?></strong>!
            </p>

            <div input material has-icon>
              <mi>password_2</mi>
              <input type="text" <?= DEV ? "value=$Authentication->code" : ""; ?> maxlength="6" autofocus name="authentication_code" placeholder="code" autocomplete="false" tabindex=1 />
            </div>

            <div outlined p18 rounded fl alistart gap=smol+>
              <mi>help_center</mi>
              <p text>If you haven't received the mail after 30 minutes, please cancel the request and create a new one.</p>
            </div>

            <div dynamic-color class="dot-container" mt mb>
              <div class="dot-pulse"></div>
              <div class="dot-pulse"></div>
              <div class="dot-pulse"></div>
            </div>

            <input type=hidden name=authentication_type value=<?= $type; ?> />
            <input type=hidden name=authentication_token
              value=<?= $Authentication->token; ?> />
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
  </div>
</form>

<?php die(success(data: ob_get_clean()));
