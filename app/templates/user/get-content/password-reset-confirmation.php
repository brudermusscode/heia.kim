<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

redirect_unauthorized(CurrentUser);

ob_start(); ?>

<div style="min-height:100vh;" fl alic jucc pblock62>
  <content smolplus>
    <box-model prompt filled=lighter elevated>
      <div prompt-content fl fldircol gap>
        <div prompt-header>
          <mi>lock_reset</mi>
          <p title>Password reset</p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <?php if (CurrentUser->email) { ?>
            <form request="password-reset:create" responder close-overlay
              um-open="security">
              <box-model submit-closest filled clickable>
                <bm-inr size=smol fl gap alistart>
                  <mi wide>alternate_email</mi>
                  <div fl fldircol gap="smoler">
                    <p text bold>E-Mail address</p>
                    <div fl gap="smol" slight>
                      <p text smol>Receive a mail with a link to verify your identity and restore your password</p>
                    </div>
                  </div>
                </bm-inr>
              </box-model>

              <input type=hidden name=email value="<?= CurrentUser->email ?>" />
            </form>
          <?php } else { ?>
            <box-model filled=darker clickable>
              <bm-inr size=std>
                <div fl fldircol gap=smol alic>
                  <mi size=mid>no_accounts</mi>
                  <p tac text>No options available</p>
                </div>
              </bm-inr>
            </box-model>
          <?php } ?>

          <tipp-box outlined=darker rounded=mid>
            <mi>info</mi>
            <p text>If none of these options can be used, please create a support ticket on our {discord-link} and we will help you there.</p>
          </tipp-box>
        </div>
      </div>

      <div prompt-actions>
        <mbutton background=clean close-overlay>
          <p text>Cancel</p>
        </mbutton>
        <div></div>
      </div>
    </box-model>
  </content>
</div>

<?php die(success(data: ob_get_clean()));
