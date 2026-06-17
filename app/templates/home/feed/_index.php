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

    <div fl fldircol gap=smol>
      <div fl alic jucsb>
        <p text bold ttup>Dev Updates</p>
        <a extern target="_blank" href="https://github.com/brudermusscode/heia.kim">
          <mbutton icon-only hoverable>
            <mi color=yellow>deployed_code</mi>
          </mbutton>
        </a>
      </div>

      <?php

      ?>

      <div posrel fl fldircol gap=smoler>

        <?php

        $show = 6 - 1;
        $count = 0;
        $commits = new RedisManager()->connection()
          ->zRange(RedisRegistry::$github_commit_history, 0, $show, [
            "WITHSCORES" => true,
            "REV",
          ]);

        if (empty($commits)) : ?>
          <div p24 tac fl fldircol alic jucc outlined rounded slight>
            <p text bold>Huch?</p>
            <p text smol>Could not fetch history</p>
          </div>
        <?php else : ?>
          <div posabs mt12 style="height:calc(100% - 42px);width:3px;left:10.4px;top:0;" rounded background=slight></div>
        <?php endif; ?>

        <?php foreach ($commits ?? [] as $key => $unix_timestamp) :
          if ($count === $show + 1) break;
          $count++;
          $commit = json_decode($key, true);

        ?>
          <a extern target="_blank" href="<?= $commit["html_url"] ?? "#" ?>"
            fl alistart gap=smol>
            <mi mt4 style="height:24px;width:24px;" background=bg z posrel circled>commit</mi>
            <div flone outlined rounded pinline14 pblock8 clickable>
              <p text smolplus semibold>
                <?= $commit["commit"]["message"] ?? "Unknown message" ?></p>
              <div fl alic gap=smol>
                <p text smol mr4>
                  <span color=company>
                    <?= Time::ago($commit["commit"]["author"]["date"] ?? CURRENT_TIMESTAMP) ?></span>
                </p>
                &middot;
                <p text smol slight fl alic>
                  <mi std>merge_type</mi> panties
                </p>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
        <div fl alic gap=smol>
          <mi std mt4 style="height:24px;width:24px;" background=bg z posrel circled>open_in_new</mi>
          <a flone extern target="_blank" href="https://github.com/brudermusscode/heia.kim">
            <mbutton mt6 background=yellow color=Dark>
              Github Repository
            </mbutton>
          </a>
        </div>
      </div>
    </div>
  </column-wrapper>
</wrapper>