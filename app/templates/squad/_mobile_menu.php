<div class="mobile_menu" shadowed=min>
  <div class="mobile_menu__inr">
    <a href="/home">
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="ri-arrow-left-line"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>Back</p>
          </div>
        </div>
      </div>
    </a>

    <?php foreach ($Squad->modes()->where("active", 1)->get() as $Mode) { ?>
      <a href="<?= $base_url . "/$Mode->mode/vanilla"; ?>" <?php if ($mode === $Mode->mode) echo "active"; ?>>
        <div class="mobile_menu__inr_option">
          <div alitcent flcol>
            <div class="icon" pinline2>
              <p text std bold>
                <i class="osu-icon osu-<?= $Mode->mode == "osu" ? "vanilla" : $Mode->mode; ?>"></i>
              </p>
            </div>
            <div class=text>
              <p text smol ttup><?= $Mode->mode; ?></p>
            </div>
          </div>
        </div>
      </a>
    <?php } ?>

    <?php if (LOGGED) { ?>
      <a href="/u/<?= $CurrentUser->id; ?>">
        <picture circled size=smol in-menu>
          <lottie-player lottie-loading-logout src="https://assets4.lottiefiles.com/packages/lf20_bdxrzm9n.json"
            background="transparent" speed="1"
            style="visibility:hidden;opacity:0;width: 8.8em; height: 8.8em;transition:all .1s linear;" loop align-center-screen>
          </lottie-player>
          <img trigger="user:image,change" src="<?= AVATAR . "/$CurrentUser->id"; ?>" />
        </picture>
      </a>
    <?php } else { ?>

      <a href="/login" style="position:relative;z-index:2;">
        <div class="mobile_menu__inr_option">
          <div alitcent flcol>
            <div class="icon" pinline2>
              <p color=green>
                <i class="mi">login</i>
              </p>
            </div>
            <div class=text color=green>
              <p text smol ttup>Login</p>
            </div>
          </div>
        </div>
      </a>
    <?php } ?>
  </div>
</div>