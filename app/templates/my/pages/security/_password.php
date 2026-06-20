<div fl fldircol gap>
  <div fl alic gap>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>
    <p text mid bold>Password</p>
  </div>

  <form request="user:update" responder responder um-open="security">
    <div fl fldircol gap=smol>
      <div fl fldircol gap=smol>
        <div input material has-icon>
          <mi>key_vertical</mi>
          <input autofocus type="password" name="current_password" enter-submitable placeholder="Current password" autocomplete="current-password" tabindex=1 />
        </div>
      </div>

      <div input material has-icon>
        <mi>asterisk</mi>
        <input type="password" name="password" enter-submitable placeholder="New password" autocomplete="new-password" tabindex=2 />
      </div>

      <div fl jucstart>
        <a request-get="user:get-content:password-reset-confirmation">
          <mbutton has-icon=left color=company no-hover-shadow hoverable>
            <mi smol>help</mi> Don't know it?
          </mbutton>
        </a>
      </div>

      <div fl jucend gap mt>
        <mbutton mid background=slight-green color=dark-green submit-closest tabindex=3>
          <p text bold>Change password</p>
        </mbutton>
      </div>
    </div>
  </form>
</div>