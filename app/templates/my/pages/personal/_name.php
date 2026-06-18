<?php

$changes_left = CurrentUser->changes_left("name");

?>

<div fl gap alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>
  <p text mid bold><?= __("Username") ?></p>
</div>

<form settings request="user:update" responder reload>
  <div fl fldircol gap>
    <div input material has-icon>
      <mi>text_fields</mi>
      <input type="text" name="name" placeholder="Username" autocomplete="false" toggle="username-taken" value="<?= CurrentUser->name; ?>" data-backup="<?= CurrentUser->name; ?>" />
      <input type=hidden data-backup value="<?= CurrentUser->name; ?>" />
    </div>

    <div fl jucsb alic gap>
      <p text>Changes left &middot;
        <strong color=company><?= CurrentUser->changes_left("name") ?></strong>
      </p>

      <?php if ($changes_left) : ?>
        <mbutton mid background=slight-green color=dark-green confirm-submit-button>
          <div action>
            <p text bold><?= __("Save") ?></p>
          </div>
          <div confirmation>
            <p text bold><?= __("Are you sure?") ?></p>
          </div>
        </mbutton>
      <?php else : ?>
        <a href="/unlock/premium">
          <mbutton background=premium color=premium-text mid has-icon=left>
            <mi><?= PREMIUM_ICON ?></mi>
            Buy more changes
          </mbutton>
        </a>
      <?php endif; ?>
    </div>
  </div>
</form>