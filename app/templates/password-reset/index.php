<?php

use Heiakim\Model\PasswordReset;

/**
 * @var string
 */
$token = filter_var(GET->token ?? "", FILTER_SANITIZE_SPECIAL_CHARS);
$token_valid = false;

/**
 * @var ?PasswordReset
 */
$token_valid
  = $PasswordReset
  = PasswordReset::where("token", $token)
  ->whereNull("updated_at")
  ->first();

/**
 * Heading
 */
include TEMPLATE . "/login/_header.php";

?>

<login>

  <?php

  if (ANIMATIONS_ENABLED) {
    echo '<div class="stars">';
    for ($i = 0; $i < 80; $i++)
      echo '<div class="snow"></div>';
    echo '</div>';
  }

  ?>

  <div disguised-content content-width=std fl fldircol gap>

    <!--- FLEX: MAIN CONTENT --->
    <div style="flex:1;flex-direction:row-reverse;" gap justcontcent>
      <div class="login_right" fl fldircol gap=mid>
        <div fl jucstart>
          <a href="/login">
            <mbutton size=mid icon-only color=light outlined material>
              <mi size=mid>arrow_back</mi>
            </mbutton>
          </a>
        </div>

        <sign-container fl fldircol gap=mid>
          <box-model filled=lighter class="sign_container__inr" elevated rounded=wide>
            <bm-inr size=wide>
              <?php if (!$token) { ?>

                <div fl fldircol gap>
                  <div fl fldircol gap=smolest>
                    <h2 text mid bold><?= __("Forgot Password?") ?></h2>
                    <p text std><?= __("Request a new one with your e-mail") ?></p>
                  </div>

                  <form
                    request="password-reset:create"
                    redirect="/login"
                    audio-success="bell-highpitch-audio"
                    audio-error="bell-negative-audio"
                    responder fl fldircol gap>
                    <div input material has-icon>
                      <i class="mi" size=std>alternate_email</i>
                      <input type="text" name="mail" placeholder="E-Mail" enter-submitable />
                    </div>

                    <div filled=darker p24 rounded=mid fl alistart gap=smol+>
                      <i class=mi size=std>info</i>
                      <p text>
                        <?= __("If you haven't signed up with a real e-mail address in the first place, please contact our support team through our Discord.") ?>
                      </p>
                    </div>

                    <div fl jucend>
                      <mbutton ripple-effect submit-closest material has-icon=left size=mid background=slight-green
                        color=dark-green>
                        <i class=mi size=std>move_to_inbox</i>
                        <p text bold><?= __("Request instructions") ?></p>
                      </mbutton>
                    </div>
                  </form>
                </div>

              <?php } else { ?>

                <div fl fldircol gap>
                  <div>
                    <div>
                      <h2 text mid bold trimt><?= __("Password reset") ?></h2>
                    </div>
                    <div>
                      <p text std>
                        <?php

                        if ($token_valid) echo __("Enter your new password");
                        else echo __("Outdated request");

                        ?>
                      </p>
                    </div>
                  </div>

                  <?php if ($token_valid) { ?>
                    <form
                      request="password-reset:update"
                      responder
                      redirect="<?= LOGGED ? "/my/security" : "/login" ?>"
                      audio-success="bell-highpitch-audio"
                      audio-error="bell-negative-audio">
                      <input type=hidden value="<?= $token; ?>" name=token>

                      <div fl fldircol gap=smol>
                        <div input material has-icon has-extra>
                          <i class="mi" size=std>face</i>
                          <input type=email value="<?= $PasswordReset->user->email; ?>" name=mail disabled>
                        </div>

                        <div input material has-icon has-extra>
                          <i class="mi" size=std>password</i>
                          <input type="password" autofocus name="password" placeholder="<?= __("Password"); ?>"
                            autocomplete="false" required enter-submitable />
                        </div>
                      </div>

                      <div fl jucend mt>
                        <mbutton ripple-effect submit-closest material size=mid background=slight-green color=dark-green>
                          <p text bold><?= __("Change it") ?></p>
                        </mbutton>

                        <div float cl></div>
                      </div>
                    </form>
                  <?php } else { ?>

                    <div>
                      <p text>
                        <?= __("An either invalid or outdated token has been used to reset a password. You might want to request a new one. If you think this is a mistake, contact a staff member through our") ?>
                        <a extern target='_blank' href='<?= _env("DISCORD_INVITE") ?>'>Discord <i class='ri-link-unlink'></i></a>.
                      </p>
                    </div>

                  <?php } ?>
                </div>
              <?php } ?>
            </bm-inr>
          </box-model>

          <?php

          /**
           * Footer.
           */
          include TEMPLATE . "/login/_footer.php"; ?>
        </sign-container>
      </div>
    </div>
  </div>
</login>