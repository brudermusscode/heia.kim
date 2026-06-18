<?php

use Heiakim\Database\RedisManager;
use Heiakim\Model\Feed;
use Heiakim\Registry\RedisRegistry;
use Heiakim\Time\Time;

/**
 * @var string $sub
 */

$base_limit = 3;
$Feed = new Feed(CurrentUser);

?>

<wrapper fl alistart jucstretch>
  <column-wrapper smol hide-mobile posstick style="top:24px;">
    <?php include __DIR__ . "/_follower-activity.php"; ?>
  </column-wrapper>

  <column-wrapper wide flone fl fldircol flex-truncate>
    <?php

    # Applications open banner.
    if (!in_array("applications_open", INFO_WINDOWS))
      include __DIR__ . "/_banner-applications-open.php"; ?>

    <!--- Newly Ranked --->
    <div fl fldircol gap=smol+>
      <div title-inline fl alic gap=smol+>
        <mbutton icon-only outlined text midler>🏅</mbutton>
        <div fl fldircol>
          <h2 text ttup><?= __("Newly ranked") ?></h2>
          <p text color=company>Beatmaps you can achieve performance on</p>
        </div>
      </div>

      <div fl fldircol gap=smol>
        <get-content from="/home/get-content/newly-ranked" fl fldircol gap=smol>
          <?php include TEMPLATE . "/beatmap/_placeholder-column.php" ?>
        </get-content>
      </div>
    </div>

    <!--- Newly Loved --->
    <div fl fldircol gap=smol+>
      <div title-inline fl alic gap=smol+>
        <mbutton icon-only outlined text midler>❤️</mbutton>
        <div>
          <h2 text ttup><?= __("Newly loved") ?></h2>
          <p text color=company>Beatmaps people like alot</p>
        </div>
      </div>
      <div fl fldircol gap=smol>
        <get-content from="/home/get-content/newly-loved" fl fldircol gap=smol>
          <?php include TEMPLATE . "/beatmap/_placeholder-column.php" ?>
        </get-content>
      </div>
    </div>

    <!--- Most Played --->
    <div fl fldircol gap=smol+>
      <div title-inline fl alic gap=smol+>
        <mbutton icon-only outlined text>
          <mi midler>trending_up</mi>
        </mbutton>
        <div>
          <h2 text ttup><?= __("Most played") ?></h2>
          <p text color=company>Beatmaps that have been played alot</p>
        </div>
      </div>
      <div fl fldircol gap=smol>
        <get-content from="/home/get-content/most-played-beatmaps" fl fldircol gap=smol>
          <?php include TEMPLATE . "/beatmap/_placeholder-column.php" ?>
        </get-content>
      </div>
    </div>
  </column-wrapper>

  <column-wrapper smol hide-tablet style="position:sticky;top:16px;">
    <?php include __DIR__ . "/_dev-updates.php" ?>
  </column-wrapper>
</wrapper>