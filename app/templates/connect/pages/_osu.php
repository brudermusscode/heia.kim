<?php

use Bruder\Heiakim\Controller\Connect\ConnectOsuController;

/**
 * Retrieve the Data from the vendor API.
 */
$Vendor = (new ConnectOsuController)->create([
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


// $User = (new User)->new((object) [
//   "name" => $Osu->user->username,
//   "email" => Utils::random_alpha_token(24),
//   "password" => Utils::random_alpha_token(24),
//   "avatar_url" => $Osu->user->avatar_url,
//   "country" => $Osu->user->country_code,
//   "vendor" => $Connect,
// ]);

?>



<login>

  <?php include SNOW; ?>

  <div disguised-content content-width=smolest fl fldircol gap>

    <!--- FLEX: MAIN CONTENT --->
    <div style="flex:1;flex-direction:row-reverse;" gap justcontcent>
      <div class="login_right" fl fldircol gap=mid flexone>
        <sign-container fl fldircol gap=mid>

          <!--- FAILED --->
          <?php if ($failed) { ?>

            <div fl fldircol lh1 alic color=light>
              <p text bold><?= __("Connect with") ?></p>
              <p text wider bold>osu!</p>
            </div>

            <box-model rounded="wide" filled="lighter">
              <bm-inr size=wide fl fldircol alic gap>
                <div tac fl fldircol gap=smoler>
                  <p text std><?= $Vendor->message; ?></p>
                </div>
                <a href="/home">
                  <mbutton material ripple-effect has-icon="left" size="mid" filled>
                    <mi>arrow_back</mi>
                    <p text std><?= __("Go home") ?></p>
                  </mbutton>
                </a>
              </bm-inr>
            </box-model>

          <?php } else { ?>


            <!--- NEW USER --->
            <?php if ($is_new_user) { ?>

              <div fl fldircol gap=smol>
                <?php if ($Vendor->is_legit) { ?>
                  <box-model background=refollow color=dark-blue rounded=wide elevated=wide>
                    <bm-inr size=std fl alistart gap="smol+">
                      <i class="mi" size="std">verified</i>
                      <p text std>
                        Your account will be whitelisted and protected from automated restrictions
                      </p>
                    </bm-inr>
                  </box-model>
                <?php } ?>

                <box-model rounded="wide" filled=lighter elevated=wide>
                  <box-model rounded=wide filled=darker pinline32>
                    <div fl jucc alic gap posrel>
                      <picture size=wide circled filled>
                        <img src="<?= $Vendor->vendor_object->user->avatar_url; ?>" />
                      </picture>

                      <dotlottie-player src="https://lottie.host/4230de2a-4591-4952-8a60-6812e5467e2a/CqIgbq0znH.json"
                        background="transparent" speed="1"
                        style="width: 300px; height: 300px;position:absolute;top:50%;left:50%;translate:-50% -50%;"
                        autoplay>
                      </dotlottie-player>

                      <mi size=mid>page_control</mi>

                      <picture size=wide circled filled=lighter>
                        <img
                          src="https://i.ppy.sh/013ed2c11b34720790e74035d9f49078d5e9aa64/68747470733a2f2f6f73752e7070792e73682f77696b692f696d616765732f4272616e645f6964656e746974795f67756964656c696e65732f696d672f75736167652d66756c6c2d636f6c6f75722e706e67"
                          loading=lazy />
                      </picture>
                    </div>
                  </box-model>

                  <form data-form="vendors:users,create">
                    <input type=hidden name=vendor_id value="<?= $Vendor->vendor_object->user->id; ?>" />
                    <input type=hidden name=vendor_email value="<?= $Vendor->vendor_object->user->username; ?>" />
                    <input type=hidden name=access_token
                      value="<?= $Vendor->vendor_object->auth->access_token; ?>" />
                    <input type=hidden name=avatar_url value="<?= $Vendor->vendor_object->user->avatar_url; ?>" />

                    <bm-inr size=wide fl fldircol gap>
                      <div fl fldircol gap=smol alic>
                        <p text mid bold tac>Hey, <span
                            text-shadow-pulse><?= $Vendor->vendor_object->user->username; ?></span>!</p>
                        <p text>Some last things before you can start your journey on {app-name}</p>
                      </div>

                      <div fl fldircol gap=smol>
                        <div input material has-icon has-extra>
                          <i class=mi size=std>face</i>
                          <input tabindex=3 type="text" name="name" placeholder="Username"
                            value="<?= $Vendor->vendor_object->user->username; ?>" required />
                        </div>

                        <div input material has-icon has-extra>
                          <i class=mi size=std>password</i>
                          <input autofocus tabindex=1 type="password" name="password" placeholder="Password" required />
                        </div>
                        <p text slight smol>You need the password to log into the game client later</p>
                      </div>

                      <div fl fldircol gap=smol>
                        <div filled="darker" p18 rounded fl alistart gap="smol+">
                          <i class="mi" size="std">privacy_tip</i>
                          <p text std>
                            You can later set an <strong>e-mail address for recovery</strong> to your new account
                          </p>
                        </div>
                      </div>

                      <div fl jucend>
                        <mbutton ripple-effect tabindex=2 submit-closest has-icon=right material size=mid background=follow
                          color=dark-green color=white>
                          <p text bold><?= __("Connect to {app-name}") ?></p>
                          <i class=mi>arrow_forward</i>
                        </mbutton>
                      </div>
                    </bm-inr>
                  </form>
                </box-model>
              </div>


              <!--- EXISTING USER --->
            <?php } else { ?>

              <redirect to="/my/security/osu"></redirect>

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