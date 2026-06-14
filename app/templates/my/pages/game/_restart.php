<?php

use Heiakim\Time\Time;
use Heiakim\Application\Feature;

/**
 * @var string $base_url
 */

$wipes_left = CurrentUser->settings->account_wipes_left;
$one_month_ts = strtotime("-1 month");
$current_ts = strtotime(date('Y-m-d H:i:s'));
$can_wipe = true;
$time_ago = false;

if (CurrentUser->settings->account_wiped_at) {
  $account_wiped_at = strtotime(CurrentUser->settings->account_wiped_at);
  $can_wipe = !(($current_ts - $account_wiped_at) < ($current_ts - $one_month_ts));
  $time_ago = Time::ago(CurrentUser->settings->account_wiped_at);
}

?>

<div fl gap=mid alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>

  <label size="mid" has-secondary>
    <div class="label__main">
      <p bold><?= __("Restart journey") ?></p>
    </div>
  </label>
</div>

<tipp-box outlined rounded=mid>
  <mi>emergency_home</mi>
  <p text>
    <?= __("Restarting will result in your <strong>account starting at 0</strong> again. It's a tough decision but can give you a new challenge. No need for creating a second account!") ?>
  </p>
</tipp-box>

<form fl fldircol gap
  data-form="authentication:create"
  data-type="user:wipe"
  data-redirect="<?= CurrentUser->link() ?>">
  <p text std tac mb mt>
    <?php

    if ($wipes_left)
      if ($can_wipe)
        echo __("You have got <strong>{wipes-left-count}</strong> restart(s) left");
      else
        echo __("Your last restart is <strong>{last-wipe-ago}</strong><br>You may only restart once every month");
    else
      echo __("You have no restarts left");

    ?>
  </p>

  <?php if ($can_wipe && $wipes_left) : ?>
    <div fl justify-content=end gap>
      <?php if (Feature::enabled("restart_journey")) : ?>
        <input type=hidden name=type value=wipe_user />
        <mbutton mid background="besure" color=dark-orange has-icon=left submit-closest>
          <mi>fingerprint</mi>
          <p text bold>Authenticate for wipe</p>
        </mbutton>
      <?php else : ?>
        <mbutton mid disabled background=slight-orange color=dark-orange>
          <p text bold>Feature disabled</p>
        </mbutton>
      <?php endif; ?>
    </div>
  <?php else : ?>
    <!--- ? --->
  <?php endif; ?>
</form>