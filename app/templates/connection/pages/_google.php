<?php

use Heiakim\Controller\Controller\Connect\ConnectGoogleController;

/**
 * Retrieve the Data from the vendor API.
 */
$Vendor = (new ConnectGoogleController)->create([
  "code" => $code,
  "state" => $state,
]);

/**
 * @var bool
 */
$failed = !$Vendor->status;

/**
 * @var bool
 */
$is_new_user = isset($Vendor->existing_user) ? !$Vendor->existing_user : true;


/**
 * Heading
 */
include TEMPLATE . "/login/_header.php";

?>

<login>

  <?php include SNOW; ?>

  <div disguised-content content-width=smolest fl fldircol gap>

    <!--- FLEX: MAIN CONTENT --->
    <div style="flex:1;flex-direction:row-reverse;" gap justcontcent>
      <div class="login_right" fl fldircol gap=mid flexone>
        <sign-container fl fldircol gap=mid>

          <div fl fldircol lh1 alic color=light>
            <p text bold>Connect with</p>
            <p text wider bold>Google</p>
          </div>

          <!--- FAILED --->
          <?php if ($failed) { ?>

            <box-model rounded="wide" filled="lighter">
              <bm-inr size=wide fl fldircol alic gap>
                <div tac fl fldircol gap=smoler>
                  <p text std><?= $Vendor->message; ?></p>
                </div>
                <a href="/home">
                  <mbutton material ripple-effect has-icon="left" size="mid" filled>
                    <mi>arrow_back</mi>
                    <p text std>Go home</p>
                  </mbutton>
                </a>
              </bm-inr>
            </box-model>

          <?php } else { ?>


            <!--- NEW USER --->
            <?php

            if ($is_new_user) {

              $avatar_url = $Vendor->vendor_object->user->picture;
              $vendor_id = $Vendor->vendor_object->user->sub;
              $access_token = $Vendor->vendor_object->auth->access_token;
              $email = $Vendor->vendor_object->user->email;

            ?>

              <form data-form="vendors:users,create">
                <box-model rounded="wide" filled=lighter fl fldircol alic gap elevated=wide>
                  <bm-inr size=wide fl fldircol gap>
                    <input type=hidden name=vendor_id value="<?= $vendor_id; ?>" />
                    <input type=hidden name=access_token value="<?= $access_token; ?>" />
                    <input type=hidden name=vendor_email value="<?= $email; ?>" />

                    <div fl jucc alic mb>
                      <picture size=wide circled filled>
                        <?php if ($avatar_url) { ?>
                          <img src="<?= $avatar_url; ?>" loading=lazy />
                          <input type=hidden name=avatar_url value="<?= $avatar_url; ?>" />
                        <?php } else { ?>
                          <img src="<?= AVATAR . "/default"; ?>" loading=lazy />
                        <?php } ?>
                      </picture>
                    </div>

                    <div fl fldircol gap=smol>
                      <div input material has-icon has-extra>
                        <i class=mi size=std>face</i>
                        <input tabindex=1 type="text" name="name" placeholder="Username" required />
                      </div>

                      <div input material has-icon has-extra>
                        <i class=mi size=std>password</i>
                        <input autofocus tabindex=2 type="password" name="password" placeholder="Password" required />
                      </div>
                      <p text slight smol>You need the password to log into the game client later</p>
                    </div>

                    <div filled="darker" p18 rounded fl alistart gap="smol+">
                      <i class="mi" size="std">privacy_tip</i>
                      <p text std>We will save your Google's <strong>e-mail address</strong> to your new account for
                        recovery purposes</p>
                    </div>

                    <div fl jucsb>
                      <mbutton ripple-effect material size=mid tabindex=4>
                        <p text smol>Cancel</p>
                      </mbutton>

                      <mbutton ripple-effect tabindex=3 submit-closest has-icon=left material size=mid background=follow
                        color=dark-green color=white>
                        <i class=mi>done_all</i>
                        <p text bold>Create account</p>
                      </mbutton>
                    </div>
                  </bm-inr>
                </box-model>
              </form>


              <!--- EXISTING USER --->
            <?php } else { ?>

              <redirect to="/my/security/google"></redirect>

            <?php } ?>
          <?php } ?>

          <?php

          /**
           * Footer.
           */
          include TEMPLATE . "/login/_footer.php"; ?>
        </sign-container>

      </div>
    </div>

  </div>
</login>