<?php

$legal_head_slogan = "";
$legal_head_title  = __("Development");

include TEMPLATE . "/legal/_header.php";

?>

<div content-width=pre-std fl fldircol content-gap mt>
  <div class="legal__page" color=light fl fldircol gap=mid>
    <div fl jucc>
      <picture style=max-width:600px;margin-bottom:-3.2em;>
        <img src="<?= IMAGE . "/legal/dev.svg" ?>" />
      </picture>
    </div>

    <p text smol bold ttup style=letter-spacing:.2em;>
      In development</p>

    <div fl fldircol gap>
      <h2>
        <?= __("It is only one guy, that is actively developing this website and it's components. It is nothing you should underestimate. It is a mountain full of work.") ?>

        <?= __("One guy coding, many others caring!") ?>
      </h2>

      <p text bold color=company>
        A big thanks goes out to all the people caring for our Discord presence, like my brother Jonathan! A second big thanks goes out to all the people joining and actually playing on our server 🙂.
      </p>

      <p text><?= __("Me, Justin from Germany, has started developing {app-name} from scratch and I keep building it as you and all the other players are using it. This means, that we are constantly in <strong>development mode</strong> and are not yet at a point, where we could talk about something like a fulfilled <i>Version 1.0.0</i>. ") ?>
      </p>

      <p text>
        <?= __("While many other private servers are using open-source websites for their business with some adjusted images, font-sizes and spacings, we have started a whole new project in a different programming language and with other goals.") ?>
      </p>
    </div>

    <div fl fldircol gap>
      <h2><?= __("What does that mean for you?") ?></h2>
      <p text>
        <?= __("You will experience bugs and errors while browsing and using features of our website, or some parts that are already public to you might not be completly implemented and will be updated in the future. An example for this is the translation system. Many parts of this website are being translated piece by piece, which will result in some areas being translated properly, while others are still in the native language (English). This is a side-effect of having an open development concept.") ?>
      </p>

      <p text>
        <?= __("It also means that you need to wait for functionality that you might wish for to be implemented and updated. Since it's just one guy doing all of the programming, it comes down to prioritization of eliminating bugs, the order of it and the functions to be next up on the list for implementation.") ?>
      </p>
    </div>

    <div fl fldircol gap>
      <h2><?= __("What you can do") ?></h2>
      <p text>
        <?= __("We ask you for your understanding and, that when you should come along something that doesn't work properly, to report it through our <a href=\"/legal/feedback\" color=company>Feedback page</a>.") ?>
      </p>
    </div>

    <div style="height:1px;background:rgba(255,255,255,.12);"></div>

    <p text smol bold ttup style=letter-spacing:.2em;><?= __("It's not just design") ?></p>

    <div fl fldircol gap>
      <h2><?= APP_NAME; ?>
        <?= __("is driven by the love to great design, so we chose the Material Design guidelines as our ultimate design goal.") ?>
      </h2>
      <p text>
        <?= __("Alot of the time spent on developing what you see floats into designing components for it. We are trying to design along with the <a href=\"https://material.io\" extern target=\"_blank\" color=company>Material Design guidelines</a> published by Google. As functions and other parts are progressing and growing, so is the design. This in combination with the open development mode will result in alot of parts of the website being redesigned multiple times, till we are really proud and fulfilled looking at it.") ?>
      </p>
    </div>
  </div>
</div>