<?php

use Heiakim\Model\Authentication;

/**
 * @var ?string
 */
$token = filter_var(get("token") ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var Authentication
 */
$Authentication = Authentication::where("token", $token)
  ->whereNull("deleted_at")
  ->first();

if (!$Authentication)
  include UNAVAILABLE;
else {

?>
  <form data-form="user:create">
    <api-outer fl fldircol jucc alic>

      <?php

      if (ANIMATIONS_ENABLED) {
        echo '<div class="stars" z>';
        for ($i = 0; $i < 80; $i++)
          echo '<div class="snow"></div>';
        echo '</div>';
      }

      ?>

      <content std fl fldircol gap=mid>
        <div fl alic fldircol jucc color=light tac>
          <p text wide bold>Almost done</p>
          <p text std>Your account needs some personalization</p>
        </div>

        <box-model filled=lighter class="sign_container__inr" elevated fl fldirrow rounded=wide>
          <bm-inr size=wide fl fldircol gap>
            <div fl jucc alic gap mt mb=smol+>
              <picture circled size=wider>
                <img src="<?= AVATAR . "/0.jpg"; ?>" loading=lazy />
              </picture>
            </div>

            <div fl fldircol gap=smol>
              <div input has-icon material>
                <i class=mi size=std>text_fields</i>
                <input autofocus tabindex=1 type="text" name="name" placeholder="Username" autocomplete="false"
                  enter-submitable required />
              </div>

              <div input has-icon material>
                <i class=mi size=std>password</i>
                <input tabindex=2 autocomplete=new-password type="password" name="password" placeholder="Password"
                  autocomplete="false" enter-submitable required />
              </div>
            </div>

            <tipp-box rounded background=slighter>
              <mi>privacy_tip</mi>
              <p text std>This combination of a username and password will be used to log into your <?= APP_NAME; ?> account</p>
            </tipp-box>

            <input type=hidden name=token value="<?= $token; ?>" />

            <div fl jucsb alic gap>
              <mbutton material tabindex=4 background=clean data-action="authentication:remove" data-token="<?= $token; ?>"
                tabindex=4>
                <p text std>Cancel</p>
              </mbutton>

              <mbutton material size=mid background=follow color=dark-green has-icon=right submit-closest tabindex=3>
                <p text bold>Finish</p>
                <mi>arrow_forward</mi>
              </mbutton>
            </div>
          </bm-inr>
        </box-model>
        </div>
    </api-outer>
  </form>

<?php

}

?>