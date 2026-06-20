<?php

use Heiakim\Model\PasswordReset;

/**
 * @var string
 */
$token = filter_var($GLOBALS["route_param_token"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$email = filter_input(INPUT_GET, "email", FILTER_VALIDATE_EMAIL);

/**
 * @var ?PasswordReset
 */
$token_valid = $PasswordReset = PasswordReset::where("token", $token)
  ->whereNull("updated_at")
  ->first() ?? false;

# + Basic header.
include TEMPLATE . "/global/_basic-header.php";

# + Snow.
include SNOW; ?>

<content begin fl jucc>
  <sign-container posrel fl fldircol gap=mid>
    <div filled=lighter elevated rounded=wide p42 fl fldircol gap>

      <?php

      # + First step, when no token is set.
      if (!$token) : ?>

        <div fl fldircol gap>
          <div fl fldircol gap=smolest>
            <h2 text wide bold><?= __("Forgot Password?") ?></h2>
            <p text><?= __("Request a new one with your e-mail") ?></p>
          </div>

          <form
            request="password-reset:create"
            redirect="/login"
            audio-success="bell-highpitch-audio"
            audio-error="bell-negative-audio"
            responder fl fldircol gap>
            <div input material has-icon>
              <mi midler>alternate_email</mi>
              <input autofocus required enter-submitable type="email" name="email" placeholder="E-Mail" value="<?= $email ?: "" ?>" />
            </div>

            <div background=slight-green color=dark-green p16 rounded fl alistart gap=smol+>
              <mi midler>info</mi>
              <p text>
                <?= __("If you haven't signed up with a real e-mail address in the first place, please contact our support team through our Discord.") ?>
              </p>
            </div>

            <div fl jucsb>
              <a onclick="history.go(-1);">
                <mbutton mid outlined ripple-effect>
                  Ouh, back!
                </mbutton>
              </a>

              <mbutton mid rounded=smol+ ripple-effect submit-closest icon-only background=green color=light>
                <mi>arrow_forward</mi>
              </mbutton>
            </div>
          </form>
        </div>

      <?php else : ?>
        <div fl fldircol gap>
          <div flone>
            <p text wide bold trimt><?= __("Password reset") ?></p>
            <p text>
              <?= $token_valid
                ? __("Enter your new password")
                : __("Outdated request") ?>
            </p>
          </div>

          <?php if ($token_valid) : ?>
            <form
              request="password-reset:update"
              responder
              redirect="<?= LOGGED ? "/home" : "/login" ?>"
              audio-success="bell-highpitch-audio"
              audio-error="bell-negative-audio">
              <div fl fldircol gap=smol>
                <div input material has-icon has-extra>
                  <mi midler>alternate_email</mi>
                  <input type=email value="<?= $PasswordReset->user->email; ?>" name=mail disabled>
                </div>

                <div input material has-icon has-extra>
                  <mi>key_vertical</mi>
                  <input type="password" autofocus name="password"
                    placeholder="<?= __("Password"); ?>"
                    autocomplete="false" required enter-submitable />
                </div>
              </div>

              <input type=hidden value="<?= $token ?>" name=token>

              <div fl jucend mt>
                <mbutton mid ripple-effect submit-closest icon-only rounded=smol+ background=green color=light>
                  <mi>check</mi>
                </mbutton>

                <div float cl></div>
              </div>
            </form>
          <?php else : ?>
            <div>
              <p text>
                <?= __("An either invalid or outdated token has been used to reset a password. You might want to request a new one. If you think this is a mistake, contact a staff member through our") ?>
                <a extern target='_blank' href='<?= _env("DISCORD_INVITE") ?>'>Discord <i class='ri-link-unlink'></i></a>.
              </p>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php include TEMPLATE . "/global/_basic-footer.php"; ?>
  </sign-container>
</content>