<?php

use Heiakim\Application\Feature;
use Heiakim\Model\User;

/**
 * @var int
 */
$user_count = User::count();

/**
 * Random Users
 */
$RandomUsers = User::limit(12)->get();

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

    <?php

    # + User is logged in, show unavailable.
    if (LOGGED) :
      include UNAVAILABLE;

    # + User is revisiting, but session has expired.
    elseif (USER_COMEBACK) :
      include __DIR__ . "/_valid_session.php";

    else: ?>

      <div fl gap=mid style=width:100%;>
        <div class="login_left" style="margin-top:1.2em;">
          <div mt=std class=login_left__slogan>
            <h1 text wide bold color=white>
              <?= __("Ready to click some circles and climb the leaderboard?") ?>
            </h1>
          </div>

          <div mt=mid>
            <p text smol bold ttup slight color=white>
              <?= "<strong>$user_count</strong> " . __("people are already!"); ?>
            </p>

            <div class="random_users">
              <?php

              foreach ($RandomUsers as $key => $User) :

                /**
                 * @var User $User
                 */

                if ($key === 6) break;

              ?>
                <a href="/u/<?= $User->id; ?>">
                  <div class="random_users__option">
                    <picture circled has-tooltip=bottom no-trans-delay>
                      <?php $User->image(); ?>
                      <div ttooltip>
                        <p text std bold><?= $User->name(); ?></p>
                      </div>
                    </picture>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="login_left__actions">
            <div>
              <p text smol ttup bold color=white style=opacity:.6;><?= __("Not a member?") ?></p>
            </div>
            <a href="/register">
              <mbutton ripple-effect background=yellow color=white size=mid material>
                <p text bold><?= __("Get started") ?></p>
              </mbutton>
            </a>
          </div>
        </div>

        <div class="login_right" flexone>
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

                  /**
                   * Single Sign On
                   */
                  include __DIR__ . "/_sso.php"; ?>
                </div>

                <div class=divider></div>

                <?php if (Feature::is_enabled("login")) { ?>

                  <form data-form="session:create" fl fldircol gap>
                    <div fl fldircol gap>
                      <div fl fldircol gap=smol+>
                        <div fl fldircol gap=smol>
                          <div input material has-icon>
                            <i icon class=mi size=std>account_circle</i>
                            <input type="text" name="login" placeholder="Username/E-Mail" enter-submitable />
                          </div>

                          <div input material has-icon>
                            <i class=mi size=std>password</i>
                            <input type="password" name="password" placeholder="<?= __("Password") ?>"
                              enter-submitable />
                          </div>
                        </div>

                        <div fl jucstart>
                          <a href="/password-reset" color=company>
                            <p text><?= __("Reset Password") ?></p>
                          </a>
                        </div>
                      </div>

                      <div class="sc__content-actions" fl jucend mt>
                        <mbutton ripple-effect submit-closest has-icon=right material size=mid background=slight-green
                          color=dark-green>
                          <p text bold><?= __("Login") ?></p>
                          <mi>east</mi>
                        </mbutton>
                      </div>
                    </div>
                  </form>

                <?php } else { ?>

                  <div tac>
                    <div justcontcent>
                      <lottie-player src="https://assets3.lottiefiles.com/private_files/lf30_glnkkfua.json"
                        background="transparent" speed="1" style="width: 120px; height: 120px;" loop autoplay>
                      </lottie-player>
                    </div>
                    <div>
                      <p text std><?= __("The login per e-mail or username has temporarily been disabled.") ?></p>
                    </div>
                  </div>

                <?php } ?>
              </bm-inr>
            </box-model>

            <?php include __DIR__ . "/_footer.php"; ?>
          </sign-container>
        </div>
      </div>

    <?php endif; ?>

  </div>
</login>