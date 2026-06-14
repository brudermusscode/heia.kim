<header top fl alic jucsb>
  <div inr flone>
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
        <mbutton mid request-get="search"
          pn-option rounded icon-only background=complement color=white>
          <i class=mi>explore</i>
        </mbutton>

        <div fl alic>
          <a href="/leaderboard">
            <mbutton mid ripple-effect background="clean" no-hover-shadow>
              <p text std bold><?= __("Rankings") ?></p>
            </mbutton>
          </a>

          <a href="/beatmaps">
            <mbutton mid ripple-effect background="clean" no-hover-shadow>
              <p text std bold>Beatmaps</p>
            </mbutton>
          </a>

          <a href="/squads">
            <mbutton mid ripple-effect background="clean" no-hover-shadow>
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
        <mbutton mid ripple-effect background="clean">
          <p text bold><?= __("Login") ?></p>
        </mbutton>
      </a>

      <a href="/register" hide-1000>
        <mbutton mid ripple-effect background=company has-icon=right color=light>
          <p text bold><?= __("Get started") ?></p>
          <mi>east</mi>
        </mbutton>
      </a>
    </div>
  </div>
</header>