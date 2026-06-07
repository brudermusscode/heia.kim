<?php

use Heiakim\Application\Feature;

$sub = filter_var(GET->sub ?? "sso", FILTER_SANITIZE_SPECIAL_CHARS);

# + Login specific header partial.
include TEMPLATE . "/global/_basic-header.php";

# + Snow.
include SNOW; ?>

<content begin fl jucc>

  <?php

  # + User is logged in.
  if (LOGGED) :
    include UNAVAILABLE;

  # + User is revisiting, but session has expired.
  elseif (USER_COMEBACK) :
    include TEMPLATE . "/global/_begin-valid-session.php";

  else : ?>

    <sign-container fl fldircol gap=mid>
      <box-model filled=lighter class="sign_container__inr" elevated fl fldircol rounded=wide>
        <bm-inr size=wide fl fldircol gap>
          <div fl fldircol gap=smolest>
            <h2 text wider bold><?= __("Sign up") ?></h2>
            <p text std><?= __("Begin your journey on") ?> <?= APP_NAME; ?></p>
          </div>

          <!--- OAuth2 sign up --->
          <div fl gap=smol>
            <mbutton
              <?= !Feature::is_enabled("connect_osu") ? "disabled" : "" ?>
              data-action="connection:start"
              data-provider="osu!"
              has-tooltip=bottom flexone material ripple-effect size=mid background="osu-pink" color=light>
              <i text wider class="osu-icon osu-outlined"></i>
              <div ttooltip>
                <p text bold>osu!</p>
              </div>
            </mbutton>

            <mbutton
              <?= !Feature::is_enabled("connect_discord") ? "disabled" : "" ?>
              data-action="connection:start"
              data-provider="discord"
              has-tooltip=bottom flexone material ripple-effect size=mid background="discord-blue" color=white>
              <i class="ri-discord-fill"></i>
              <div ttooltip>
                <p text bold>Discord</p>
              </div>
            </mbutton>

            <mbutton
              <?= !Feature::is_enabled("connect_github") ? "disabled" : "" ?>
              data-action="connection:start"
              data-provider="github"
              has-tooltip=bottom no-delay flexone material ripple-effect size=mid background="invert" color=invert>
              <i class="ri-github-fill"></i>
              <div ttooltip>
                <p text bold>GitHub</p>
              </div>
            </mbutton>
          </div>

          <div class=divider></div>

          <!--- E-Mail sign up --->
          <form request="authentication:create" redirect="/login" responder fl gap=smol>
            <div input material has-icon flexone>
              <mi midler>alternate_email</mi>
              <input required autofocus enter-submitable type="text" name="email" placeholder="E-Mail" />
            </div>

            <input type=hidden name=type value="user:create" />

            <mbutton material size=mid submit-closest icon-only rounded=smol background=green color=light>
              <mi>arrow_forward</mi>
            </mbutton>
          </form>

          <p text std>
            <?= __("By using any of the above methods, you agree to our") ?>
            <?= __("<a href=\"/legal/privacy\" color=company>Privacy Policies</a>") ?>
            <?= __("and those of possible third party services.") ?>
          </p>

          <div fl <?= $sub !== "sso" ? "jucsb" : "jucend"; ?> mt>
            <a href="/login">
              <mbutton ripple-effect material size=mid outlined>
                <?= __("Back to login") ?>
              </mbutton>
            </a>
          </div>
        </bm-inr>
      </box-model>

      <?php

      # + Login sepcific footer with basic links.
      include TEMPLATE . "/global/_basic-footer.php"; ?>
    </sign-container>

  <?php endif; ?>

</content>