<error-page background=dark>

  <?php include SNOW; ?>

  <div class="floating_container" color=light>
    <div fl fldircol alic style="max-width:600px;">

      <?php

      /**
       * E-Mail notification has been unsubscribed from.
       */
      if (defined("VALID_MAILING_UNSUBSCRIBE")) { ?>
        <dotlottie-player src="https://lottie.host/c0e62ae2-7e73-424f-b29d-63c85d55a00a/PIXkBweISf.json"
          background="transparent" speed="1" style="width: 220px; height: 220px;" loop autoplay>
        </dotlottie-player>

        <div mb=mid tac fl fldircol gap=smol+>
          <p text bold wide lh=smol>Your suffering is over!</p>
          <p text>You have unsubscribed from <strong>this mailing.</strong></p>
        </div>

        <div fl fldircol gap=smol+>
          <tipp-box rounded=mid background=dynamic color=dynamic>
            <mi>privacy_tip</mi>
            <p text>If you want to unsubscribe from all mailings that <?= APP_NAME; ?> is sending out to you,
              please
              <a normal href="/login">login</a> and go to your Mailing settings under Data & Privacy to turn any off you
              wish for.
            </p>
          </tipp-box>

          <div fl jucstart>
            <mbutton material size=mid filled=lighter has-icon=left color=dynamic onclick="history.go(-1);">
              <mi>west</mi>
              <p text bold>Go back</p>
            </mbutton>
          </div>
        </div>

      <?php

      } else if (CURRENT_PAGE === "u" && isset($_GET["id"]) && $_GET["id"] == 0) {

      ?>

        <dotlottie-player src="https://lottie.host/ee952c85-630f-4232-9b6b-513774fd0b4c/JBwBavSh3J.json"
          background="transparent" speed="1" style="width: 300px; height: 300px;margin-bottom:1.8em;" loop autoplay>
        </dotlottie-player>

        <div mb=mid tac fl fldircol gap=smol+>
          <p text bold wide lh=smol>Have you seen them?</p>
          <p text>This user has left us.</p>
        </div>

        <div fl fldircol gap=smol+>
          <tipp-box rounded=mid background=dynamic color=dynamic>
            <mi>privacy_tip</mi>
            <p text>Some information saved won't be deleted after the removal of your user account, mostly for context.
              But
              any information that could be connected to your user identity will be removed. For more information, we
              refer
              to our {privacy-policy-link}.</p>
          </tipp-box>

          <div fl jucstart>
            <mbutton material size=mid filled=lighter has-icon=left color=dynamic onclick="history.go(-1);">
              <mi>west</mi>
              <p text bold>Go back</p>
            </mbutton>
          </div>
        </div>

      <?php

        /**
         * Any other error that could occur and is not being catched.
         */
      } else { ?>
        <dotlottie-player src="https://lottie.host/c4ccee2a-f8c5-4b98-80d1-070eaf1d6b7d/pY1TEzyuMv.json"
          background="transparent" speed="1" style="width: 500px; height: 500px;margin-bottom: -6em;margin-top:-8em;" loop
          autoplay></dotlottie-player>

        <div mb=mid tac>
          <p text bold wider>Meow!</p>
          <p text midler>You have entered an unknown path. It's too dangerous to go further, better turn back!</p>
        </div>

        <div fl fldircol gap=smol+>
          <div fl jucstart>
            <mbutton material size=mid filled=lighter has-icon=left color=dynamic onclick="history.go(-1);">
              <mi>west</mi>
              <p text bold>Go back</p>
            </mbutton>
          </div>
        </div>
      <?php } ?>

    </div>
  </div>
</error-page>