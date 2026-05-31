<?php

$name_changes_left = CurrentUser->changes_left("name");

?>

<div mb fl gap alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>

  <label size="mid" has-secondary>
    <div class="label__main">
      <p bold><?= __("Username") ?></p>
    </div>
  </label>
</div>

<tipp-box outlined rounded=mid>
  <mi>privacy_tip</mi>
  <p text>
    <?= __("Your username is visible to any other player. You can change it up to <strong>{name-changes-count} time(s)</strong>") ?>
  </p>
</tipp-box>

<form settings request="user:update" responder reload>
  <div fl fldircol gap>
    <div input material has-icon>
      <mi>text_fields</mi>
      <input type="text" name="name" placeholder="Username" autocomplete="false" toggle="username-taken" value="<?= CurrentUser->name; ?>" data-backup="<?= CurrentUser->name; ?>" />
      <input type=hidden data-backup value="<?= CurrentUser->name; ?>" />
    </div>

    <div fl justify-content=end gap>
      <mbutton background=slight-green color=dark-green size=mid material confirm-submit-button>
        <div action>
          <p text bold><?= __("Save") ?></p>
        </div>
        <div confirmation>
          <p text bold><?= __("Are you sure?") ?></p>
        </div>
      </mbutton>
    </div>
  </div>
</form>