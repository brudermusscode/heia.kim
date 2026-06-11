<?php

use Heiakim\Application\Feature;

/**
 * @var bool
 */
$user_signed_up_with_osu =
  !filter_var(USER_COMEBACK->email, FILTER_VALIDATE_EMAIL)
  && USER_COMEBACK->osu;

?>

<div fl jucc>
  <sign-container style=max-width:420px; posrel flexone>

    <box-model style=background:transparent>
      <bm-inr size=std>
        <form data-form="session:create" fl fldircol gap>
          <div fl fldircol gap=smol+ alic mb>
            <picture circled size=wide>
              <?php USER_COMEBACK->image(gdpr: false); ?>
            </picture>
            <p text mid bold color=light><span
                <?php if (ANIMATIONS_ENABLED) echo "text-shadow-pulse"; ?>><?= USER_COMEBACK->name; ?></span></p>
            <input type=hidden name=login value="<?= USER_COMEBACK->name; ?>" />
          </div>

          <div fl gap=smol>
            <?php

            /**
             * Single Sign On
             */
            include __DIR__ . "/_sso.php"; ?>
          </div>

          <?php if (USER_COMEBACK->connections->count()) { ?>
            <div class=divider dark></div>
          <?php } ?>

          <?php if (Feature::enabled("login")) { ?>
            <div fl fldircol gap=mid>
              <div fl fldircol gap=smol+>
                <div fl gap=smol alic>
                  <div input material has-icon flexone>
                    <i class=mi size=std>password</i>
                    <input type="password" name="password" placeholder="<?= __("Password") ?>" enter-submitable />
                  </div>
                  <mbutton ripple-effect submit-closest icon-only material size=mid background=follow color=dark-green>
                    <mi>arrow_forward</mi>
                  </mbutton>
                </div>

                <div fl jucstart>
                  <a href="/password-reset" color=company>
                    <p text><?= __("Reset Password") ?></p>
                  </a>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <div filled p24 pinline42 rounded fl fldircol alic gap=smol+>
              <div filled=lighter circled style="height:3.2em;width:3.2em;" fl alic jucc>
                <mi size=mid>switches</mi>
              </div>
              <p text std bold tac><?= __("Login has temporarily been disabled") ?></p>
            </div>
          <?php } ?>
        </form>

        <div style="height:1px;background:rgba(255,255,255,.08);" mt=mid></div>
      </bm-inr>
    </box-model>

    <?php include __DIR__ . "/_footer.php"; ?>
  </sign-container>
</div>