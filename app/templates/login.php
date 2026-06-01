<?php

use Heiakim\Application\Feature;
use Heiakim\Model\User;
use Illuminate\Support\Collection;

$user_count = User::count();

/**
 * @var Collection<User>
 */
$RandomUsers = User::limit(12)->get();

# + Login specific header partial.
include TEMPLATE . "/global/_basic-header.php";

# + Snow.
include SNOW; ?>

<content begin fl jucc>

  <?php

  # + User is logged in, show unavailable.
  if (LOGGED) :
    include UNAVAILABLE;

  # + User is revisiting, but session has expired.
  elseif (USER_COMEBACK) :
    include TEMPLATE . "/global/_begin-valid-session.php";

  else: ?>

    <sign-container posrel fl fldircol gap=mid>
      <div style="position:absolute;top:-7.4em;right:-6.4em;rotate: 28deg;display:none;">
        <picture style="height:16em;width:16em;">
          <img src="<?= IMAGE . "/crhat.png"; ?>" />
        </picture>
      </div>

      <box-model filled=lighter class="sign_container__inr" elevated rounded=wide>
        <bm-inr size=wide fl fldircol gap>
          <div fl fldircol gap=smolest>
            <h1 text wide bold><?= __("Login") ?></h1>
            <section>
              <p text std><?= __("Resume your journey on") ?> <?= APP_NAME; ?></p>
            </section>
          </div>

          <div fl gap=smol>
            <?php

            # + osu!
            if (
              USER_COMEBACK && USER_COMEBACK->osu
              || !USER_COMEBACK && Feature::is_enabled("connect_osu")
            ) : ?>

              <mbutton flexone
                <?= (USER_COMEBACK && USER_COMEBACK->connections->count() < 2) ? "has-icon=left" : "has-tooltip=bottom"; ?>
                material ripple-effect size=mid background="osu-pink" data-action="vendors:login" data-vendor=osu color=light>
                <i text wider class="osu-icon osu-outlined"></i>
                <?php if (USER_COMEBACK && USER_COMEBACK->connections->count() < 2) { ?>
                  <p text>Login with <strong>osu!</strong></p>
                <?php } else { ?>
                  <div ttooltip>
                    <p text bold>osu!</p>
                  </div>
                <?php } ?>
              </mbutton>

            <?php endif;

            # + Discord
            if (
              USER_COMEBACK && USER_COMEBACK->discord
              || !USER_COMEBACK && Feature::is_enabled("connect_discord")
            ) :
              echo <<<TEXT
                <mbutton has-tooltip=bottom flexone material ripple-effect  size=mid background="discord-blue" data-action="vendors:login" data-vendor=discord color=white>
                  <i class="ri-discord-fill"></i>
                  <div ttooltip>
                    <p text bold>Discord</p>
                  </div>
                </mbutton>
              TEXT;
            endif;

            # + Google
            if (
              USER_COMEBACK && USER_COMEBACK->google
              || !USER_COMEBACK && Feature::is_enabled("connect_google")
            ) :
              echo <<<TEXT
                <mbutton has-tooltip=bottom no-delay flexone material ripple-effect  size=mid background="invert" data-action="vendors:login" data-vendor=google color=invert>
                  <i class="ri-google-fill"></i>
                  <div ttooltip>
                    <p text bold>Google</p>
                  </div>
                </mbutton>
              TEXT;
            endif; ?>
          </div>

          <div class=divider></div>

          <?php if (Feature::is_enabled("login")) { ?>

            <form data-form="session:create" fl fldircol gap>
              <div fl fldircol gap>
                <div fl fldircol gap=smol+>
                  <div fl fldircol gap=smol>
                    <div input material has-icon>
                      <mi midler>sticker</mi>
                      <input required autofocus enter-submitable type="text" name="login"
                        placeholder="Username/E-Mail" />
                    </div>

                    <div input material has-icon>
                      <mi midler>key_vertical</mi>
                      <input required enter-submitable type="password" name="password"
                        placeholder="<?= __("Password") ?>" />
                    </div>
                  </div>

                  <div fl jucstart>
                    <a href="/password-reset" color=company>
                      <p text><?= __("Reset Password") ?></p>
                    </a>
                  </div>
                </div>

                <div fl jucsb mt>
                  <a href="/register">
                    <mbutton outlined ripple-effect material size=mid>
                      Create account
                    </mbutton>
                  </a>

                  <mbutton ripple-effect submit-closest icon-only material size=mid rounded=smol
                    background=green color=light>
                    <mi>arrow_forward</mi>
                  </mbutton>
                </div>
              </div>
            </form>

          <?php } else { ?>

            <div tac>
              <p text std><?= __("The login per e-mail or username has temporarily been disabled.") ?></p>
            </div>

          <?php } ?>
        </bm-inr>
      </box-model>

      <?php

      # + Login specific footer with basic links.
      include TEMPLATE . "/global/_basic-footer.php"; ?>
    </sign-container>

  <?php endif; ?>

</content>