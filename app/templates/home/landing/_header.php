<menu class="menu_main__top">
  <div class="menu_main__top_inr">
    <div class=left>
      <div class="logo">
        <div main-logo class="main__logo">
          <a href="/home">
            <picture quadrat style="height:4em;width:4em;" loading>
              <img src="<?= IMAGE . '/logo/painted/100.webp'; ?>" loaded=true />
            </picture>
          </a>

          <div class="path-loader">
            <svg class="circular" viewBox="25 25 50 50">
              <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4 " stroke-miterlimit="10" />
            </svg>
          </div>
        </div>
      </div>

      <div class=menu fl alic gap=smol>
        <mbutton hide-1200 ripple-effect background=complement icon-only material rounded color=white size=mid
          data-action="search:open" has-tooltip=bottom>
          <mi>search</mi>
          <div ttooltip>
            <p text bold><?= __("Press F to search") ?></p>
          </div>
        </mbutton>

        <div fl alic>
          <a href="/leaderboard">
            <mbutton size=mid ripple-effect background="clean" material no-hover-shadow>
              <p text std bold><?= __("Rankings") ?></p>
            </mbutton>
          </a>

          <a href="/beatmaps">
            <mbutton size=mid ripple-effect background="clean" material no-hover-shadow>
              <p text std bold>Beatmaps</p>
            </mbutton>
          </a>

          <a href="/squads">
            <mbutton size=mid ripple-effect background="clean" material no-hover-shadow>
              <p text std bold>Squads</p>
            </mbutton>
          </a>
        </div>
      </div>
    </div>

    <div hide-800 class=right fl alic>
      <div has-tooltip=bottom>
        <?php include TEMPLATE . "/components/ui/_theme_switcher.php"; ?>

        <div ttooltip>
          <p text bold><?= __("Switch color mode") ?></p>
        </div>
      </div>

      <a href="/login">
        <mbutton ripple-effect background="clean" size=mid material>
          <p text bold><?= __("Login") ?></p>
        </mbutton>
      </a>

      <a href="/register" hide-1000>
        <mbutton ripple-effect background="yellow" size=mid has-icon=right material color="white">
          <p text bold><?= __("Get started") ?></p>
          <mi>east</mi>
        </mbutton>
      </a>
    </div>
  </div>
</menu>