<?php

# + Landing header.
include __DIR__ . "/landing/_header.php";

# + Snow.
include SNOW; ?>

<content home fl fldircol alistretch>
  <div inr fl fldircol gap flone>
    <div fl alic jucc>
      <div class="big_title">
        <p>We love</p>
        <div class="text-carousel">
          <p class=text-container></p>
        </div>
      </div>

      <dotlottie-wc src="https://lottie.host/540f0e28-590a-4eaf-adb5-900b30b91e8f/M2HiBpksQx.lottie" autoplay loop></dotlottie-wc>
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