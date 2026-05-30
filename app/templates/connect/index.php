<?php

use Bruder\Heiakim\Controller\Connect\ConnectController;

/**
 * Get the type from the url parameter.
 */
$type = filter_var(get("type"));

/**
 * Pass the type to the GET super global.
 */
$_GET["type"] = $type;

/**
 * @var object
 */
$Controller = (new ConnectController($_GET))->create();

if (!$Controller->status) :

  pdie($Controller);

  include UNAVAILABLE;
else :

  /**
   * Include dependencies kurwa.
   */
  include SNOW;
  include TEMPLATE . "/login/_header.php";

  /**
   * @var bool
   */
  $is_new_user = false;

?>

  <login>
    <div disguised-content content-width=smolest fl fldircol gap>

      <!--- FLEX: MAIN CONTENT --->
      <div style="flex:1;flex-direction:row-reverse;" gap justcontcent>
        <div class="login_right" fl fldircol gap=mid flexone>
          <sign-container fl fldircol alic jucc gap=mid>

            <!--- NEW USER --->
            <?php if ($is_new_user) { ?>

              <div fl fldircol gap=smol alic color=light>
                <p text bold>Connect with</p>
                <picture style=height:2.4em;>
                  <img src="<?= IMAGE . "/vendor/discord-logo-white.png"; ?>" loading=lazy />
                </picture>
              </div>

              <form data-form="vendors:users,create">
                <box-model rounded="wide" filled=lighter fl fldircol alic gap elevated=wide>
                  <bm-inr size=wide fl fldircol gap>
                    <input type=hidden name=vendor_id value="<?= $Vendor->vendor_object->user->id; ?>" />
                    <input type=hidden name=access_token
                      value="<?= $Vendor->vendor_object->auth->access_token; ?>" />
                    <input type=hidden name=vendor_email value="<?= $Vendor->vendor_object->user->email; ?>" />

                    <div fl jucc alic mb>
                      <picture size=wide circled filled>
                        <?php if ($Vendor->vendor_object->user->avatar_url) { ?>
                          <img src="<?= $Vendor->vendor_object->user->avatar_url; ?>" loading=lazy />
                          <input type=hidden name=avatar_url
                            value="<?= $Vendor->vendor_object->user->avatar_url; ?>" />
                        <?php } else { ?>
                          <img src="<?= AVATAR . "/default"; ?>" loading=lazy />
                        <?php } ?>
                      </picture>
                    </div>

                    <div fl fldircol gap=smol>
                      <div input material has-icon has-extra>
                        <i class=mi size=std>face</i>
                        <input tabindex=1 type="text" name="name" placeholder="Username"
                          value="<?= $Vendor->vendor_object->user->username; ?>" required />
                      </div>

                      <div input material has-icon has-extra>
                        <i class=mi size=std>password</i>
                        <input autofocus tabindex=2 type="password" name="password" placeholder="Password" required />
                      </div>
                      <p text slight smol>You need the password to log into the game client later</p>
                    </div>

                    <div filled="darker" p18 rounded fl alistart gap="smol+">
                      <i class="mi" size="std">privacy_tip</i>
                      <p text std>We will save your Discord's <strong>e-mail address</strong> to your new account for
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

              <dotlottie-wc
                src="https://lottie.host/d4242e69-28a6-469c-a497-9101bd6682bf/JLC47Liv8f.lottie"
                style="width: 400px;height: 400px;margin-bottom:-6.2em;"
                speed="1"
                autoplay
                loop></dotlottie-wc>

              <div fl fldircol alic jucc>
                <p text bold mid>Success brother!</p>
                <p text>… Redirecting you back in <span text bold color=company id="counter"></span> …</p>
              </div>

              <redirect to="/my/security/<?= $type ?>" delay=5000></redirect>

              <script>
                let sec = 5
                let el = document.getElementById("counter")

                el.innerHTML = sec;

                let timer = setInterval(() => {
                  sec--
                  el.textContent = sec
                  if (sec <= 0) clearInterval(timer)
                }, 1000)
              </script>

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

<?php endif;
