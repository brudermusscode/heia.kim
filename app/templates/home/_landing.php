<?php include __DIR__ . "/landing/_header.php"; ?>

<div class="home_content">
  <div class="home_content__inr">

    <?php

    if (ANIMATIONS_ENABLED) {
      echo '<div class="stars">';
      for ($i = 0; $i < 80; $i++)
        echo '<div class="snow"></div>';
      echo '</div>';
    }

    ?>

    <div class="home_content__inr_main">
      <div>
        <div class="spaced_flex">
          <p text midler slight mb=smol bold show-800><?= APP_NAME; ?></p>
          <div class="big_title">
            <p>We love</p>
            <div class="text-carousel">
              <div>
                <p class=text-container></p>
              </div>
            </div>
          </div>

          <div class="actions" fl alic gap=smol+>
            <a extern target=_blank href="https://discord.gg/WYsnsUzYcR">
              <mbutton ripple-effect material hide-800 has-icon=left size=wide background=discord-blue rounded=wider
                color=white>
                <i class="ri-discord-fill" text wide></i>
                <p text mid bold>@heia.kim</p>
              </mbutton>
            </a>
          </div>
        </div>
      </div>

      <div class="lottie">
        <dotlottie-wc
          src="https://lottie.host/540f0e28-590a-4eaf-adb5-900b30b91e8f/M2HiBpksQx.lottie"
          style="width: 400px;height: 400px"
          autoplay
          loop></dotlottie-wc>
      </div>
    </div>
  </div>

  <footer style=margin:0; class="home_content__sub">
    <div fl juc fldircol alic gap=smol+ title-inline tac>
      <h1 text midler fl alic gap=smol><?= __("An osu! private server, with ❤️ made in Germany.") ?></h1>

      <?php include __DIR__ . "/_legal_links.php"; ?>
    </div>
  </footer>
</div>