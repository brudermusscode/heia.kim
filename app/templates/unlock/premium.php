<?php

use Heiakim\Time\Time;
use Heiakim\Application\Feature;

/**
 * @var string
 */
$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_SPECIAL_CHARS);

# + Success message.
if ($action == "success") :
  include __DIR__ . "/_success.php";

# + Error message.
elseif ($action == "error") :
  include __DIR__ . "/_error.php";

# + Base view.
else :

  $premium_time_left = Time::left(CurrentUser->donor_end) ?? null;
  $premium_feature_enabled = Feature::enabled("buy_premium_feature");

  # + Snow.
  include SNOW; ?>

  <content fl fldircol alic jucc>
    <div tac mb=wide>
      <p text wider>Unlock <strong><?= APP_SETTING->premium_feature_name; ?></strong></p>
      <p text midler>It's better than Tinder Gold.</p>
    </div>

    <box-model filled=lighter class="pricing_plan">
      <div class="plan_inr">
        <div class="plan_header" fl alistart gap=smol+ pinline42 pblock38 rounded=wide>
          <mi wider mt4><?= PREMIUM_ICON; ?></mi>
          <div>
            <p text wide bold>Essential</p>
            <p text std><strong>1 month</strong> unlocked features</p>
          </div>
        </div>

        <div fl fldircol gap pinline42 pblock32>
          <p tac text widest bold>
            <?= number_format(APP_SETTING->premium_feature_price, 2, ","); ?> €</p>

          <div fl fldircol gap=smol>
            <div class="option">
              <mi></mi>
              <p text>Custom profile headlines</p>
            </div>
            <div class="option">
              <mi></mi>
              <p text>Unique Name Designs</p>
            </div>
            <div class="option">
              <mi></mi>
              <p text>Added Name Change</p>
            </div>
            <div class="option">
              <mi></mi>
              <p text>Added Account Wipe</p>
            </div>
            <div class="option">
              <mi></mi>
              <p text>Upload GIFs</p>
            </div>
            <div class="option" error>
              <?php if (!CurrentUser->discord) : ?>
                <div has-tooltip=bottom curpo>
                  <mi></mi>
                  <div ttooltip>
                    <p text>Requires a Discord connection</p>
                  </div>
                </div>
                <div fl alic gap=smol>
                  <p text>Role on Discord
                    (<a href="/my/security/discord" normal>Connect</a>)</p>
                </div>
              <?php else : ?>
                <mi></mi>
                <p text>Discord role</p>
              <?php endif; ?>

            </div>
          </div>

          <?php if (!$premium_time_left) { ?>
            <div class=info style=margin-bottom:-.4em; background=slighter rounded=wide pblock18 pinline24 pr18 fl gap=smol+>
              <mi>tips_and_updates</mi>
              <p text smol>You still have <strong><?= $premium_time_left; ?></strong> of your
                <?= APP_SETTING->premium_feature_name; ?>. Buying more will be added on top.</p>
            </div>
          <?php } ?>

          <div mt18 fl jucc alic gap=smol+>
            <a href="/home">
              <mbutton background=clean material>Cancel</mbutton>
            </a>
            <?php if ($premium_feature_enabled) : ?>
              <form data-form="orders:paypal,create">
                <input type=hidden name=months value=1 />
                <mbutton size=mid background=slight-green color=dark-green has-icon material submit-closest>
                  <p text>Donate with &nbsp;
                    <strong><i class=ri-paypal-fill style="font-size:18px;"></i> PayPal</strong>
                  </p>
                </mbutton>
              </form>
            <?php else : ?>
              <mbutton size=std background=green has-icon material disabled>
                <div fl align-items=center gap=smol>
                  <p text std color=white>Currently disabled</p>
                </div>
              </mbutton>
            <?php endif; ?>
          </div>
        </div>

        <div class="plan_bottom" fl fldircol alic gap=smol jucc>
          <p text std>Stops automatically</p>
        </div>
      </div>
    </box-model>
  </content>

<?php endif; ?>