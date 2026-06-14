<?php

use Heiakim\Application\Cookie;

?>

<footer mb100>
  <div class="page-end-logo" mt=wide mb=wide>
    <div data-react="scroll:reached-end" justcontcent mt=wide style=display:none;>
      <?php include_once COMPONENT . "/ui/_circular_loader.html"; ?>
    </div>

    <div page-end-bird justcontcent>
      <picture size=mid quadrat id=logo-end>
        <img src="<?= IMAGE . "/logo/painted/100.webp"; ?>" />
      </picture>
    </div>
  </div>

  <?php if (!IS_EDIT_MODE) { ?>

    <div dno content-width=wide fl fldircol gap>
      <div background=slighter style=height:1px;></div>

      <div flex-wrap=reverse fl alic jucsb gap>
        <div fl alic gap flex-wrap>
          <p text midler bold><?= APP_NAME; ?></p>
          <a href="/legal/privacy">
            <p text midler semi-bold><?= __("Privacy") ?></p>
          </a>
          <a href="/legal/imprint">
            <p text midler semi-bold><?= __("Responsible") ?></p>
          </a>
          <a href="/legal/team">
            <p text midler semi-bold><?= __("The Team") ?></p>
          </a>
          <?php

          $consent_step_cookie = Cookie::exists("POLICIES_CONSENT_STEP") ? Cookie::get("POLICIES_CONSENT_STEP") : "index";

          if (LOGGED && !CurrentUser->privacy->accepts_policies) {
            $consent_url = "/legal/consent/$consent_step_cookie";

          ?>
            <a href="<?= $consent_url; ?>">
              <p text midler semi-bold><?= __("Policy consent") ?></p>
            </a>
          <?php } ?>
          <!-- <p text midler semi-bold disabled><?= __("Feedback") ?></p> -->
        </div>

        <div fl gap=smol flex-wrap=wrap alic>
          <a href="<?= _env("DISCORD_INVITE") ?>" extern target="_blank">
            <mbutton mid has-icon=left filled=lighter color=dynamic>
              <i class="ri-discord-fill"></i>
              <p text std bold>@heia.kim</p>
            </mbutton>
          </a>
          <a href="https://www.youtube.com/@heia.kimosu" extern target="_blank">
            <mbutton mid has-icon=left filled=lighter color=dynamic>
              <i class="ri-youtube-fill"></i>
              <p text std bold>@heia.kimosu</p>
            </mbutton>
          </a>
        </div>
      </div>

      <div fl flex-wrap=wrap jucsb alic gap=mid>
        <?php include __DIR__ . "/_languages.php"; ?>
        <?php if (!LOGGED) { ?>
          <div rounded=wide filled=dark color=dynamic p8 style=padding-left:32px;>
            <?php include TEMPLATE . "/global/_join_now.php"; ?>
          </div>
        <?php } ?>
      </div>

      <div>
        <p text smol slight>Some icons and images were obtained from <a normal href="https://www.freepik.com">freepik.com</a>
        </p>
      </div>
    </div>

  <?php } ?>

</footer>