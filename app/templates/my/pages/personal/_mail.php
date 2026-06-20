<?php

use Heiakim\Model\Authentication;

/**
 * @var string $category
 * @var string $sub
 * @var string $var
 */

$validation_mail = str_replace(" ", "+", $var);

/**
 * @var ?Authentication
 */
$Authentication = CurrentUser->authentications()
  ->where([
    "email" => $validation_mail
  ])
  ->first();

$email = !filter_var(CurrentUser->email, FILTER_VALIDATE_EMAIL)
  ? null
  : CurrentUser->email;

?>

<div fl gap alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>
  <p text mid bold>E-mail address</p>
</div>

<tipp-box outlined rounded=mid>
  <mi>privacy_tip</mi>
  <p text>Your e-mail address is only visible to you. It's required for authentication.</p>
</tipp-box>

<form responder
  data-form="authentication:create"
  data-type="user:update:email"
  fl fldircol gap=smol+>
  <?php if (CurrentUser->email) { ?>
    <p text>Current e-mail address is <strong><?= CurrentUser->email; ?></strong></p>
  <?php } else { ?>
    <p text>You have no e-mail address set, but we <strong>highly recommend to set one</strong>. It is required to properly authenticate any critical action to your account, as well as recovering it if you lose your credentials.</p>
  <?php } ?>

  <div fl fldircol gap>
    <div input material has-icon>
      <mi>alternate_email</mi>
      <input autofocus required type="text" name="value" enter-submitable placeholder="<?= $email ?? "E-mail address"; ?>" autocomplete="false" tabindex=1 />
    </div>

    <div fl justify-content=end gap>
      <mbutton mid background=besure has-icon=left color=dark-orange submit-closest tabindex=2>
        <mi>fingerprint</mi>
        <p text bold>Request code</p>
      </mbutton>
    </div>
  </div>
</form>