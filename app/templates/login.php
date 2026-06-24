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

      <div filled=lighter elevated rounded=wide p42 fl fldircol gap>
        <div fl fldircol gap=smolest>
          <h2 text wider bold><?= __("Login") ?></h2>
          <section>
            <p text std><?= __("Resume your journey on") ?> <?= APP_NAME; ?></p>
          </section>
        </div>

        <div fl gap=smol>
          <mbutton mid ripple-effect
            <?= !Feature::enabled("connect_osu") ? "disabled" : "" ?>
            data-action="connection:start"
            data-provider="osu!"
            data-call-action="reconnect"
            flexone has-tooltip=bottom background="osu-pink" color=light>
            <i text class="osu-icon osu-outlined"></i>
            <div ttooltip>
              <p text bold>osu!</p>
            </div>
          </mbutton>

          <mbutton mid ripple-effect
            <?= !Feature::enabled("connect_discord") ? "disabled" : "" ?>
            data-action="connection:start"
            data-provider="discord"
            data-call-action="reconnect"
            has-tooltip=bottom flexone background="discord-blue" color=white>
            <i class="ri-discord-fill"></i>
            <div ttooltip>
              <p text bold>Discord</p>
            </div>
          </mbutton>

          <mbutton mid ripple-effect
            <?= !Feature::enabled("connect_github") ? "disabled" : "" ?>
            data-action="connection:start"
            data-provider="github"
            data-call-action="reconnect"
            has-tooltip=bottom no-delay flexone background="invert" color=invert>
            <i class="ri-github-fill"></i>
            <div ttooltip>
              <p text bold>GitHub</p>
            </div>
          </mbutton>
        </div>

        <div class=divider></div>

        <?php if (Feature::enabled("login")) { ?>

          <form data-form="session:create" fl fldircol gap>
            <button type=submit></button>
            <div fl fldircol gap>
              <div fl fldircol gap=smol+>
                <div fl fldircol gap=smol>
                  <div input material has-icon>
                    <mi midler>sticker</mi>
                    <input required autofocus enter-submittable type="text" name="login" tabindex=1
                      placeholder="Username/E-Mail" />
                  </div>

                  <div input material has-icon>
                    <mi midler>key_vertical</mi>
                    <input required enter-submitable type="password" name="password"
                      tabindex=2 placeholder="<?= __("Password") ?>" />
                  </div>
                </div>

                <div fl jucstart>
                  <a tabindex=4 href="/password-reset" color=company>
                    <p text><?= __("Reset Password") ?></p>
                  </a>
                </div>
              </div>

              <div fl jucsb mt>
                <a href="/register" tabindex=5>
                  <mbutton mid outlined ripple-effect>
                    Create account
                  </mbutton>
                </a>

                <mbutton mid ripple-effect submit-closest icon-only rounded=smol
                  tabindex=3 background=green color=light>
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
      </div>

      <?php

      # + Login specific footer with basic links.
      include TEMPLATE . "/global/_basic-footer.php"; ?>
    </sign-container>

  <?php endif; ?>

</content>