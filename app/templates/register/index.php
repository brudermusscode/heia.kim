<?php

/**
 * GET Parameter.
 */
$sub = filter_var(GET->sub ?? "sso", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Heading
 */
include TEMPLATE . "/login/_header.php";

?>

<login>

  <?php include SNOW; ?>

  <div disguised-content content-width=smolest fl fldircol gap>

    <?php

    # + User is logged in.
    if (LOGGED) :
      include UNAVAILABLE;

    # + User is revisiting, but session has expired.
    elseif (USER_COMEBACK) :
      include TEMPLATE . "/login/_valid_session.php";

    else : ?>

      <!--- FLEX: MAIN CONTENT --->
      <div style="flex:1;flex-direction:row-reverse;" gap justcontcent>
        <div class="login_right" fl fldircol gap=mid flexone>
          <sign-container fl fldircol gap=mid>
            <box-model filled=lighter class="sign_container__inr" elevated fl fldircol rounded=wide>
              <bm-inr size=wide fl fldircol gap>
                <div fl fldircol gap=smolest mb>
                  <h2 text wide bold><?= __("Sign up") ?></h2>
                  <p text std><?= __("Begin your journey on") ?> <?= APP_NAME; ?></p>
                </div>

                <?php

                /**
                 * @var string
                 */
                $file_path = __DIR__ . "/pages/_$sub.php";

                include file_exists($file_path) ? $file_path : __DIR__ . "/page/_sso.php";

                ?>

                <div fl <?= $sub !== "sso" ? "jucsb" : "jucend"; ?> mt>
                  <?php if ($sub !== "sso") { ?>
                    <a href="/register">
                      <mbutton ripple-effect material size=mid has-icon=left outlined>
                        <mi>arrow_back</mi>
                        <p text smol bold><?= __("Use SSO") ?></p>
                      </mbutton>
                    </a>
                  <?php } ?>

                  <a href="/login">
                    <mbutton ripple-effect material size=mid outlined>
                      <p text smol bold><?= __("Back to login") ?></p>
                    </mbutton>
                  </a>
                </div>

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

    <?php endif; ?>

  </div>
</login>