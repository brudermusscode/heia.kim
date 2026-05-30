<?php

use Bruder\Application\Feature;

/**
 * ? Osu
 */
if (USER_COMEBACK && USER_COMEBACK->osu || !USER_COMEBACK && Feature::is_enabled("connect_osu")) { ?>

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

<?php

}

/**
 * ? Discord
 */
if (USER_COMEBACK && USER_COMEBACK->discord || !USER_COMEBACK && Feature::is_enabled("connect_discord"))
  echo <<<TEXT
    <mbutton has-tooltip=bottom flexone material ripple-effect  size=mid background="discord-blue" data-action="vendors:login" data-vendor=discord color=white>
      <i class="ri-discord-fill"></i>
      <div ttooltip>
        <p text bold>Discord</p>
      </div>
    </mbutton>
  TEXT;

/**
 * ? Google
 */
if (USER_COMEBACK && USER_COMEBACK->google || !USER_COMEBACK && Feature::is_enabled("connect_google"))
  echo <<<TEXT
    <mbutton has-tooltip=bottom no-delay flexone material ripple-effect  size=mid background="invert" data-action="vendors:login" data-vendor=google color=invert>
      <i class="ri-google-fill"></i>
      <div ttooltip>
        <p text bold>Google</p>
      </div>
    </mbutton>
  TEXT;
