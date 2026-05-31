<?php

$birthday_set = CurrentUser->settings->birthday;

?>

<div fl fldircol gap>
  <div mb fl gap alic>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>

    <label size="mid" has-secondary>
      <div class="label__main">
        <p bold><?= __("Birthday") ?></p>
      </div>
    </label>
  </div>

  <tipp-box outlined rounded=mid>
    <mi>privacy_tip</mi>
    <p text>
      <?= __("Your birthday is only visible to you. But we might update the appearance of your profile or add gimmicks on the day of your birth, to celebrate with you!") ?>
    </p>
  </tipp-box>

  <form settings request="user:settings:update" reload responder>
    <div fl fldircol gap>
      <?php if (!$birthday_set) { ?>
        <div>
          <p text std><?= __("Your birthday should look like <strong>06-10-1997</strong>") ?></p>
        </div>
        <div fl gap=smol>
          <div input material>
            <input type=text max-length=2 min-length=2 name=day value placeholder="dd" autofocus tabindex=1 />
          </div>

          <div input material>
            <input type=text max-length=2 min-length=2 name=month value placeholder="mm" tabindex=2 />
          </div>

          <div input material>
            <input type=text max-length=4 min-length=4 name=year value placeholder="yyyy" tabindex=3 />
          </div>
        </div>

        <div fl justify-content=end gap>
          <mbutton size=mid background=slight-green color=dark-green material confirm-submit-button tabindex=4>
            <div action>
              <p text bold><?= __("Save") ?></p>
            </div>
            <div confirmation>
              <p text bold><?= __("Are you sure?") ?></p>
            </div>
          </mbutton>
        </div>

      <?php } else { ?>

        <div fl justify-content=center>
          <p text mid bold>
            <?= date_format(date_create(CurrentUser->settings->birthday), 'd F Y'); ?>
          </p>
        </div>

        <div fl justify-content=end gap>
          <mbutton size=mid background=besure color=dark-orange material disabled>
            <div action>
              <p text bold>Cool 🥰</p>
            </div>
          </mbutton>
        </div>

      <?php } ?>
    </div>
  </form>
</div>