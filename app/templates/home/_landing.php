<?php

# + Landing header.
include __DIR__ . "/landing/_header.php";

# + Snow.
include SNOW; ?>

<content home fl fldircol alistretch>
  <div inr flone>
    <div class="home_content__inr_main">
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

      <div class="lottie">
        <dotlottie-wc
          src="https://lottie.host/540f0e28-590a-4eaf-adb5-900b30b91e8f/M2HiBpksQx.lottie"
          style="width: 400px;height: 400px;" autoplay loop></dotlottie-wc>
      </div>
    </div>
  </div>

  <footer fl fldircol gap=smoler>
    <h1 text midler>
      <?= __("An osu! private server, with ❤️ made in Germany.") ?></h1>

    <?php

    # + Linkings to legal pages.
    include __DIR__ . "/_legal_links.php"; ?>
  </footer>
</content>