<?php

use Heiakim\Time\Time;
use Heiakim\Application\Cookie;

$ranked_last_update = Time::ago(APP_SETTING->ranked_updated_at);

include_once TEMPLATE . "/global/_mobile_menu.php";

?>

<div reload-page-icon dno>
  <mbutton elevated size=mid rounded icon-only material background=besure color=dark-orange color=white>
    <i class=mi>refresh</i>
  </mbutton>
</div>

<header main dno>
  <div fl fldircol alic>
    <div main-logo class="main__logo">
      <a href="/home" z>
        <picture quadrat style="height:3.4em;width:3.4em;" loading>
          <img src="<?php echo IMAGE . '/logo/painted/100.webp'; ?>" loaded=true />
        </picture>
      </a>

      <div class="path-loader">
        <svg class="circular" viewBox="25 25 50 50">
          <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4 " stroke-miterlimit="10" />
        </svg>
      </div>
    </div>

    <mbutton size=mid rounded icon-only material background=complement data-action="search:open" color=white>
      <i class=mi>search</i>
    </mbutton>
  </div>

  <div class="main__menu" mid>
    <div class="page">
      <a href="/home" page=feed <?php if (!CURRENT_PAGE || CURRENT_PAGE === 'home') echo "active=true"; ?>>
        <div ripple-effect class="icon">
          <i class="mi" size=midler>browse</i>
        </div>
      </a>
      <div class="hover_card" alignment="center">
        <div class="hover_card__inr" text-only>

          <?php

          $app_name = APP_NAME;

          if (LOGGED) {
            $feed_text = __("global.header.feed_text_1");
            echo <<<TEXT
            <div mb=smol>
              <p text mid bold>Feed</p>
            </div>
            <div>
              <p text std>$feed_text</p>
            </div>
          TEXT;
          } else {
            $beginning_text = __("Beginning");
            $feed_text = __("global.header.feed_text_2");
            echo <<<TEXT
              <div mb=smol>
                <p text mid bold>$beginning_text</p>
              </div>
              <div>
                <p text std>$feed_text</p>
              </div>
            TEXT;
          }

          ?>
        </div>
      </div>
    </div>

    <div class="page">
      <a href="/leaderboard/osu/vanilla/performance/global" page=leaderboard <?php if (CURRENT_PAGE === 'leaderboard') echo "active=true"; ?>>
        <div ripple-effect class="icon">
          <i class="mi" size=midler>emoji_events</i>
        </div>
      </a>
      <div class="hover_card" alignment="center">
        <div class="hover_card__inr" text-only>
          <div mb=smol>
            <p text mid bold><?php echo __("Rankings") ?></p>
          </div>
          <div>
            <p text std style="line-height:1.2em;"><?php echo __("Rankings of all modes, updated weekly") ?></p>
          </div>
          <div fl jucend>
            <div mt>
              <div updated-new>
                <p text std>
                  <?php echo __("Updated") ?> <strong><?php echo $ranked_last_update; ?></strong>
                  <?php echo __("ago") ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="page">
      <a href="/beatmaps" page=beatmaps <?php if (in_array(CURRENT_PAGE, ['beatmaps', "beatmapset"])) echo "active=true"; ?>>
        <div ripple-effect class="icon">
          <i class="mi" size=midler>web_stories</i>
        </div>
      </a>
      <div class="hover_card" alignment="center">
        <div class="hover_card__inr" text-only>
          <div mb=smol>
            <p text mid bold>Beatmaps</p>
          </div>
          <div>
            <p text std style="line-height:1.2em;"><?php echo __("Scroll through our huge collection of beatmaps") ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="page" disabled>
      <a href="/artists" page=artists <?php if (in_array(CURRENT_PAGE, ['artists', "artist"])) echo "active=true"; ?>>
        <div ripple-effect new=artists class=icon>
          <i class="mi" size=midler>stars</i>
        </div>
      </a>
      <div class="hover_card" alignment="center">
        <div class="hover_card__inr" text-only>
          <div mb=smol>
            <!-- <div updated-new style="top:1.8em;right:1.8em;position:absolute;">NEW</div> -->
            <p text mid bold><?php echo __("Artists") ?></p>
          </div>
          <div>
            <p text std style="line-height:1.2em;">
              <?php echo __("All the artists you can find through our beatmap collection") ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="page">
      <a href="/squads" page=squads <?php if (in_array(CURRENT_PAGE, ['squads', "squad"])) echo "active=true"; ?>>
        <div ripple-effect class="icon" new=squads>
          <i class="mi" size=midler>workspaces</i>
        </div>
      </a>
      <div class="hover_card" alignment="center">
        <div class="hover_card__inr" text-only>
          <div mb=smol>
            <div updated-new class=ttup style="top:1.8em;right:1.8em;position:absolute;">
              <?php echo __("global.updated.page") ?>
            </div>
            <p text mid bold>Squads</p>
          </div>
          <div>
            <p text std style="line-height:1.2em;"><?php echo __("Join a squad and enjoy playing together") ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="main__user">
    <menu-js-content></menu-js-content>

    <?php

    if (empty($_COOKIE["COOKIE_CONSENT"]))
      include __DIR__ . "/dialogues/_cookie_consent.php";

    ?>

    <div data-react="user:sign" style="position:relative;z-index:10;">
      <?php include TEMPLATE . "/components/header/_menu.php"; ?>
    </div>

    <div has-tooltip=right>
      <?php include TEMPLATE . "/components/ui/_theme_switcher.php"; ?>

      <div ttooltip>
        <p text bold><?php echo __("Switch color mode") ?></p>
      </div>
    </div>
  </div>
</header>