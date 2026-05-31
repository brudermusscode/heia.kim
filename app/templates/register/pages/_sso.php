<?php

use Heiakim\Application\Feature;

?>

<!--- SIGN UP WITH API --->
<div fl gap=smol>
  <?php

  /**
   * ? Osu
   */
  if (Feature::is_enabled("connect_osu"))
    echo <<<TEXT
        <mbutton has-tooltip=bottom flexone material ripple-effect size=mid background="osu-pink" data-action="vendors:osu,auth,create" color=light>
          <i text wider class="osu-icon osu-outlined"></i>
          <div ttooltip>
            <p text bold>osu!</p>
          </div>
        </mbutton>
      TEXT;

  /**
   * ? Discord
   */
  if (Feature::is_enabled("connect_discord"))
    echo <<<TEXT
        <mbutton has-tooltip=bottom flexone material ripple-effect  size=mid background="discord-blue" data-action="vendors:discord,auth,create" color=white>
          <i class="ri-discord-fill"></i>
          <div ttooltip>
            <p text bold>Discord</p>
          </div>
        </mbutton>
      TEXT;

  /**
   * ? Google
   */
  if (Feature::is_enabled("connect_google"))
    echo <<<TEXT
        <mbutton has-tooltip=bottom no-delay flexone material ripple-effect  size=mid background="invert" data-action="vendors:google,auth,create" color=invert>
          <i class="ri-google-fill"></i>
          <div ttooltip>
            <p text bold>Google</p>
          </div>
        </mbutton>
      TEXT;

  ?>
</div>

<div class=divider></div>

<a href="/register/email">
  <mbutton material filled=darker has-icon=left size=mid>
    <mi>alternate_email</mi>
    <p text bold><?= __("Use E-Mail") ?></p>
  </mbutton>
</a>

<p text std>
  <?= __("By using any of the above methods, you agree to our") ?>
  <?= __("<a href=\"/legal/privacy\" color=company>Privacy Policies</a>") ?>
  <?= __("and those of possible third party services.") ?>
</p>