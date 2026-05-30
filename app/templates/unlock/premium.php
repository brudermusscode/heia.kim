<?php

use Bruder\Time\Time;
use Bruder\Application\Application;

$action = $get_params->action ?? "";
$token = $get_params->token ?? "";

if ($action == "success")
  include __DIR__ . "/_success.php";
else if ($action == "error")
  include __DIR__ . "/_error.php";
else {

  $premium_time_left = Time::left($CurrentUser->donor_end) ?? null;

  /**
   * Feature disabled?
   */
  $premium_feature_enabled = Application::get_features("buy_premium_feature")->active;

?>

  <div class="pricing_plans">
    <?php

    if (ANIMATIONS_ENABLED) {
      echo '<div class="stars">';
      for ($i = 0; $i < 80; $i++)
        echo '<div class="snow"></div>';
      echo '</div>';
    }

    ?>

    <div tac mb=wide>
      <p text wider>Unlock <strong><?= APP_SETTING->premium_feature_name; ?></strong></p>
      <p text midler>It's better than Tinder Gold.</p>
    </div>

    <box-model filled=lighter class="pricing_plan">
      <div class="plan_inr">
        <div class="plan_header">
          <div class="plan_header__text" fl alistart gap=smol+>
            <i class=mi size=wide><?= PREMIUM_ICON; ?></i>
            <div>
              <p text wide bold>Essential</p>
              <p text std><strong>1 month</strong> unlocked features</p>
            </div>
          </div>
        </div>

        <div class="plan_content">
          <div class="price">
            <p><?= number_format(APP_SETTING->premium_feature_price, 2, ","); ?> €</p>
          </div>

          <div class="options">
            <div class="option">
              <p class=icon>
                <i class=mi size=mid>task_alt</i>
              </p>
              <p class=name>Custom profile headlines</p>
            </div>
            <div class="option">
              <p class=icon>
                <i class=mi size=mid>task_alt</i>
              </p>
              <p class=name>Unique Name Designs</p>
            </div>
            <div class="option">
              <p class=icon>
                <i class=mi size=mid>task_alt</i>
              </p>
              <p class=name>Added Name Change</p>
            </div>
            <div class="option">
              <p class=icon>
                <i class=mi size=mid>task_alt</i>
              </p>
              <p class=name>Added Account Wipe</p>
            </div>
            <div class="option">
              <p class=icon>
                <i class=mi size=mid>task_alt</i>
              </p>
              <p class=name>Upload GIFs</p>
            </div>
            <div class="option">
              <?php if (!$CurrentUser->discord) { ?>
                <div has-tooltip=bottom>
                  <p class=icon>
                    <mi size=mid color=orange>error</mi>
                  </p>
                  <div ttooltip>
                    <p text bold>Requires a Discord connection</p>
                  </div>
                </div>
                <div fl alic gap=smol>
                  <p class=name>Discord role</p>
                  <a href="/my/security/discord" normal fl alic gap=smol>
                    <p text fl alic gap=smol>Connect</p>
                  </a>
                </div>
              <?php } else { ?>
                <p class=icon>
                  <i class=mi size=mid>task_alt</i>
                </p>
                <div>
                  <p class=name>Discord role</p>
                </div>
              <?php } ?>

            </div>
          </div>

          <?php if ($premium_time_left) { ?>
            <div class=info mt style=margin-bottom:-.4em; background=slighter rounded=wide pblock24 pinline24>
              <div fl gap>
                <p text mid normalize-icon>
                  <i class=mi>tips_and_updates</i>
                </p>
                <p text smol>You still have <strong><?= $premium_time_left; ?></strong> of your
                  <?= APP_SETTING->premium_feature_name; ?>. Buying more will be added on top.</p>
              </div>
            </div>
          <?php } ?>

          <div <?= $premium_time_left ? "mt=mid" : "mt=wide"; ?> fl jucc alic gap=smol+>
            <a href="/home">
              <mbutton background=clean material>
                <div fl align-items=center gap=smol>
                  <p text smol>Cancel</p>
                </div>
              </mbutton>
            </a>
            <?php if ($premium_feature_enabled) { ?>
              <form data-form="orders:paypal,create">
                <input type=hidden name=months value=1 />
                <mbutton size=mid background=slight-green color=dark-green has-icon material submit-closest>
                  <p text>Donate with <strong><i class=ri-paypal-fill></i> PayPal</strong></p>
                </mbutton>
              </form>
            <?php } else { ?>
              <mbutton size=std background=green has-icon material disabled>
                <div fl align-items=center gap=smol>
                  <p text std color=white>Currently disabled</p>
                </div>
              </mbutton>
            <?php } ?>
          </div>
        </div>

        <div class="plan_bottom" fl fldircol alic gap=smol jucc>
          <p text std>Stops automatically</p>
        </div>
      </div>
    </box-model>
  </div>

<?php } ?>