<?php

/**
 * @var string
 */
$provider = filter_var($GLOBALS["route_param_provider"], FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_SPECIAL_CHARS);

# + Snow.
include SNOW; ?>

<content begin fl fldircol alic jucc>

  <div style="margin-bottom:-2.4em;height:300px;width:300px;">
    <?php

    # + Loading animation.
    include TEMPLATE . "/global/_lottie-pixelghost.html"; ?>
  </div>

  <p text wide bold>Wait a little</p>

  <request action="connection:create" method="POST"
    data-provider="<?= $provider ?>"
    data-code="<?= $code ?>"
    data-state="<?= $state ?>"
    redirect-from-data></request>

  <div dno>
    <?php

    # + New user registering.
    if ($is_new_user) : ?>

      <div fl fldircol gap=smol alic color=light>
        <p text bold>Connect with</p>
        <picture style=height:2.4em;>
          <img src="<?= IMAGE . "/vendor/$provider-logo-white.png"; ?>" loading=lazy />
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


    <?php

    # + Existing user connecting.
    else : ?>

      <div fl fldircol alic jucc>
        <p text bold mid>Success brother!</p>
        <p text>… Redirecting you back in <span text bold color=company id="counter"></span> …</p>
      </div>

      <!--<redirect to="/my/security/<?= $type ?>" delay=5000></redirect>-->

      <!--<script>
      let sec = 5
      let el = document.getElementById("counter")

      el.innerHTML = sec;

      let timer = setInterval(() => {
        sec--
        el.textContent = sec
        if (sec <= 0) clearInterval(timer)
      }, 1000)
    </script>-->

    <?php endif; ?>
  </div>

</content>