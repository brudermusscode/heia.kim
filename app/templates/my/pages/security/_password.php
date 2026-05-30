<?php

use Bruder\Heiakim\Model\Authentication;
use Bruder\Heiakim\Model\User;

/**
 * @var User $CurrentUser
 */

?>

<div content-width=smol>
  <div mt=wide mb fl gap=mid align-items="center" mb=std>
    <?php

    /**
     * Back button
     */
    include TEMPLATE . "/my/_back_button.php"; ?>

    <label size="mid" has-secondary>
      <div class="label__main">
        <p bold>Password</p>
      </div>
    </label>
  </div>

  <div fl fldircol gap>
    <tipp-box outlined rounded=mid>
      <mi>info</mi>
      <p text>You need your password to log into the game client and any other service of {app-name}. Make sure to
        <strong>use a secure one</strong>.
      </p>
    </tipp-box>

    <box-model filled=lighter>
      <form request="user:update" responder responder redirect="<?= $base_url; ?>">
        <bm-inr size=wide fl fldircol gap=smol>
          <div fl fldircol gap=smol>
            <div input material has-icon>
              <mi>key</mi>
              <input autofocus type="password" name="current_password" enter-submitable placeholder="Current password" autocomplete="current-password" tabindex=1 />
            </div>
          </div>

          <div input material has-icon>
            <mi>password</mi>
            <input type="password" name="password" enter-submitable placeholder="New password" autocomplete="new-password" tabindex=2 />
          </div>

          <div fl jucstart>
            <a request-get="user:get-content:password-reset-confirmation">
              <mbutton material has-icon=left color=company no-hover-shadow hoverable>
                <mi smol>help</mi> Don't know it?
              </mbutton>
            </a>
          </div>

          <div fl jucend gap mt>
            <mbutton size=mid background=slight-green color=dark-green material submit-closest tabindex=3>
              <p text bold>Change password</p>
            </mbutton>
          </div>
        </bm-inr>
      </form>
    </box-model>
  </div>
</div>