<?php

/**
 * This file handles the normal user creation through an e-mail address, as
 * well as the creation through a vendor's oauth integration.
 */

use Heiakim\Model\Authentication;

/**
 * @var ?string
 */
$token = filter_var($GLOBALS["route_param_token"], FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?string
 */
$name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?int
 */
$connection_id = filter_input(INPUT_GET, "connection_id", FILTER_VALIDATE_INT);

/**
 * @var Authentication
 */
$Authentication = Authentication::where("token", $token)
  ->whereNull("deleted_at")
  ->first();

if (!$Authentication) :
  include UNAVAILABLE;
else :

  # + Snow.
  include SNOW;

  # + Basic header.
  include TEMPLATE . "/global/_basic-header.php"; ?>

  <form data-form="user:create">
    <content begin minlineauto std fl fldircol jucsb alic gap>
      <div fl alic fldircol jucc color=light tac>
        <p text wide bold>Almost done</p>
        <p text std>Your account needs some personalization</p>
      </div>

      <div filled=lighter elevated fl fldircol gap rounded=wide p42>
        <div fl jucc alic gap mt mb=smol+>
          <picture circled size=wider>
            <img src="<?= AVATAR . "/0.jpg"; ?>" loading=lazy />
          </picture>
        </div>

        <div fl fldircol gap=smol>
          <div input has-icon material>
            <mi midler>sticker</mi>
            <input autofocus enter-submitable required tabindex=1 type="text" name="name" placeholder="Username" autocomplete="false" value="<?= $name ?: "" ?>" />
          </div>

          <div input has-icon material>
            <mi midler>key_vertical</mi>
            <input enter-submitable required tabindex=2 autocomplete=new-password type="password" name="password" placeholder="Password" autocomplete="false" />
          </div>
        </div>

        <tipp-box rounded background=slighter>
          <mi>privacy_tip</mi>
          <p text std>This combination of a username and password will be used to log into your <?= APP_NAME; ?> account</p>
        </tipp-box>

        <input type=hidden name=connection_id value="<?= $connection_id ?>" />
        <input type=hidden name=token value="<?= $token; ?>" />

        <div fl jucsb alic gap>
          <mbutton material size=mid tabindex=4 background=clean
            data-action="authentication:delete"
            data-token="<?= $token; ?>">
            <p text std>Cancel</p>
          </mbutton>

          <mbutton submit-closest material icon-only size=wide background=green tabindex=3>
            <mi>arrow_forward</mi>
          </mbutton>
        </div>
      </div>

      <?php

      # + Basic footer with links.
      include TEMPLATE . "/global/_basic-footer.php"; ?>
    </content>
  </form>

<?php endif; ?>