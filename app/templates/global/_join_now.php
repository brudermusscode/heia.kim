<div fl alistretch gap>
  <div no-word-wrap>
    <?php if (!USER_COMEBACK) { ?>
      <p text bold><?= __("Not a member?") ?></p>
      <p text smol><?= __("Join us now!") ?></p>
    <?php } else { ?>
      <p text bold><?= __("Hi") ?>, <?= USER_COMEBACK->name; ?>!</p>
      <p text smol><?= __("Resume your journey") ?></p>
    <?php } ?>
  </div>

  <div fl gap=smoler alic>
    <?php if (USER_COMEBACK && USER_COMEBACK->osu || !USER_COMEBACK) { ?>
      <mbutton icon-only background=osu-pink color=light has-tooltip=top
        <?= USER_COMEBACK?->osu
          ? 'data-action="vendors:login" data-vendor=osu'
          : 'data-action="vendors:osu,auth,create"';
        ?>>
        <mi size=mid class="osu-icon osu-outlined"></mi>
        <div ttooltip>
          <p text bold>osu!</p>
        </div>
      </mbutton>
    <?php } ?>

    <?php if (USER_COMEBACK && USER_COMEBACK->discord || !USER_COMEBACK) { ?>
      <mbutton icon-only background=discord-blue color=light has-tooltip=top
        <?= USER_COMEBACK?->discord
          ? 'data-action="vendors:login" data-vendor=discord'
          : 'data-action="vendors:discord,auth,create"';
        ?>>
        <mi size=midler class="ri-discord-fill"></mi>
        <div ttooltip>
          <p text bold>Discord</p>
        </div>
      </mbutton>
    <?php } ?>

    <?php if (USER_COMEBACK && USER_COMEBACK->google || !USER_COMEBACK) { ?>
      <mbutton icon-only background=dark color=light has-tooltip=top
        <?= USER_COMEBACK?->google
          ? 'data-action="vendors:login" data-vendor=google'
          : 'data-action="vendors:google,auth,create"';
        ?>>
        <mi size=midler class="ri-google-fill"></mi>
        <div ttooltip>
          <p text bold>Google</p>
        </div>
      </mbutton>
    <?php } ?>


    <?php

    /**
     * This should only be shown when the user has used either one
     * of the single sign on methods to sign up.
     */
    if (USER_COMEBACK && USER_COMEBACK->signed_up_through_sso()) { ?>
      <p pblock8 text smol slight bold><?= __("or") ?></p>
    <?php } ?>

    <?php if (USER_COMEBACK && filter_var(USER_COMEBACK->email, FILTER_VALIDATE_EMAIL)) { ?>
      <a href="/login">
        <mbutton icon-only background=dark color=white>
          <mi>login</mi>
        </mbutton>
      </a>
    <?php } else { ?>
      <a href="/register/email">
        <mbutton icon-only background=slight color=dynamic>
          <mi>email</mi>
        </mbutton>
      </a>
    <?php } ?>
  </div>
</div>