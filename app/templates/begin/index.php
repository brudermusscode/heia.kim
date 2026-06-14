<?php

/**
 * This file handles the normal user creation through an e-mail address, as
 * well as the creation through a vendor's oauth integration.
 */

use Heiakim\Model\Authentication;
use Heiakim\Model\Connection;

/**
 * @var ?string
 */
$token = filter_var($GLOBALS["route_param_token"], FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?string
 */
$name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var bool
 */
$name_in_use = filter_input(INPUT_GET, "name_in_use", FILTER_VALIDATE_BOOL);

/**
 * @var Authentication
 */
$Authentication = Authentication::where("token", $token)
  ->whereNull("deleted_at")
  ->first();

if (!$Authentication) :
  include UNAVAILABLE;
else :

  /**
   * @var ?Connection
   */
  $Connection = $Authentication->connection;

  # + Snow.
  include SNOW;

  # + Basic header.
  include TEMPLATE . "/global/_basic-header.php"; ?>

  <form data-form="user:create">
    <content begin minlineauto std fl fldircol jucsb alic gap>
      <div filled=lighter elevated fl fldircol gap rounded=wide p42>

        <div>
          <p text wide bold>Almost done</p>
          <p text std>Your account needs some personalization</p>
        </div>

        <?php

        # + Name is in use.
        if ($name && $name_in_use) : ?>
          <tipp-box rounded background=slight-red color=dark-red>
            <mi>sentiment_dissatisfied</mi>
            <p text std>Your name <strong><?= $name ?></strong> is in use already, sorry my friend!</p>
          </tipp-box>
        <?php endif; ?>

        <div fl fldircol gap=smol>
          <div input has-icon material>
            <mi midler>sticker</mi>
            <input autofocus enter-submitable required tabindex=1 type="text"
              name="name" placeholder="Username" autocomplete="false"
              value="<?= $name && !$name_in_use ? $name : "" ?>" />
          </div>

          <div input has-icon material>
            <mi midler>key_vertical</mi>
            <input enter-submitable required tabindex=2 autocomplete=new-password
              type="password" name="password" placeholder="Password"
              autocomplete="false" />
          </div>
        </div>

        <divide horiz></divide>

        <?php

        $email = $Connection?->provider_user_email ?? $Authentication->email ?? "";
        $email_from_text = $Connection ? "From " . $Connection->provider : "From step before";

        ?>

        <div fl fldircol gap=smol>
          <?php if ($email) : ?>
            <p text smol bold ttup><?= $email_from_text ?></p>
          <?php endif ?>
          <div input has-icon material <?= $email ? "disabled" : "" ?>>
            <mi midler>alternate_email</mi>
            <input enter-submitable required tabindex=2 autocomplete=new-password
              type="email" name="email" placeholder="E-mail address"
              value="<?= $email ?>" autocomplete="false" />
          </div>
        </div>

        <tipp-box rounded background=slighter>
          <mi>privacy_tip</mi>
          <p text std>
            We need a <strong>valid email address</strong> in case you lose your password or want to authorize any access to your account later.</p>
        </tipp-box>

        <input type=hidden name=token value="<?= $token; ?>" />

        <div fl jucsb alic gap>
          <mbutton mid tabindex=4 background=clean
            request="authentication:delete"
            data-token="<?= $token; ?>"
            shadow-submit redirect="/">
            <p text std>Cancel</p>
          </mbutton>

          <mbutton mid submit-closest tabindex=3 icon-only rounded=smol+
            background=green color=light>
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