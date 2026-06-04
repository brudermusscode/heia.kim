<?php

require_once ROOT . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\User;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * User isn't logged?
 * ! Error
 */
if (!LOGGED)
  exit($Request->error("!NOT_LOGGED"));

/**
 * @var ?User
 */
$User = User::find($id) ?? CurrentUser;

/**
 * User doesn't exist when an id is set?
 * ! Error
 */
if ($id && !$User)
  exit($Request->error());

/**
 * @var bool
 */
$want_gift = $User->id !== CurrentUser->id;

/**
 * Begin output buffer.
 */
ob_start();

?>

<form data-form="orders:paypal,create">
  <div content-width=smolest prompt-height>

    <box-model prompt elevated rounded=wide filled=lighter>
      <div prompt-content>

        <div prompt-header>
          <mi><?= PREMIUM_ICON; ?></mi>
          <p title><?= $want_gift ? "Gift" : "Buy"; ?> <?= PREMIUM_NAME; ?></p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <?php

          /**
           * Include the report banner if the current user wants to
           * gift premium to someone else.
           */
          if ($want_gift) :
            include TEMPLATE . "/user/_user_report.php";
          else :

          ?>

            <div fl fldircol gap=smol>
              <p text bold>What you get</p>

              <?php include TEMPLATE . "/unlock/_benefits_collapsed.php"; ?>
            </div>

          <?php endif; ?>

          <div fl fldircol gap=smol>
            <p text bold>How many months?</p>
            <div input has-icon material>
              <mi>date_range</mi>
              <input tabindex=1 type="number" min=1 max=12 value=1 name="months" placeholder="12" autocomplete="false" required />
            </div>
          </div>

          <tipp-box outlined=darker rounded=mid dno>
            <mi>info</mi>
            <p text>With your donation, you support the existence of <?= APP_NAME; ?>. We are grateful for every
              penny spent!</p>
          </tipp-box>

          <p text slight>By proceeding, you agree with our <a href='/legal/privacy' normal>Privacy Policy</a>. It's a
            donation and you help us staying
            existent! We are grateful for every penny spent.</p>

          <?php if ($want_gift) { ?>
            <input type=hidden name=user_id value=<?= $User->id; ?> />
          <?php } ?>
        </div>
      </div>

      <div prompt-actions>
        <mbutton material background=clean close-overlay>
          <p text>Cancel</p>
        </mbutton>
        <mbutton material size=mid has-icon=left background=follow color=dark-green submit-closest>
          <mi class="ri-paypal-fill"></mi>
          <p text bold>Donate with PayPal</p>
        </mbutton>
      </div>
    </box-model>

  </div>
</form>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
