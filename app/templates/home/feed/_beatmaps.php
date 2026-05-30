<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Score;
use Bruder\Heiakim\Model\Beatmap;

/**
 * @var User $CurrentUser
 */

/**
 * @var ?Beatmap
 */
$FavoriteBeatmaps = $CurrentUser->favorite_beatmaps;

/**
 * @var int
 */
$has_favorite_beatmaps = $FavoriteBeatmaps->count();

?>

<feed-section>
  <div class="feed_section__inr" fl fldircol gap=smol+>
    <div title-inline>
      <p text mid bold><?= __("Beatmaps you liked") ?></p>
    </div>

    <div class="feed_section__content" fl fldircol content-gap>
      <div grid-repeat gap=smol>
        <?php if (!$has_favorite_beatmaps) { ?>
          <div grid-keeper>
            <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap style="flex:1;">
              <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
                <i class="mi" size="wide">web_stories</i>
              </div>
              <div tac>
                <p text bold wide><?= __("No beatmaps") ?></p>
                <p text std><?= __("You haven't liked any beatmaps.") ?></p>
              </div>
              <div fl jucc gap=smol>
                <a href="/beatmaps">
                  <mbutton material size=mid has-icon=left filled>
                    <mi>explore</mi>
                    <p text bold><?= __("Explore Beatmaps") ?></p>
                  </mbutton>
                </a>
                <mbutton data-action="search:open" material size=mid has-icon=left filled>
                  <mi>search</mi>
                  <p text bold><?= __("Search") ?></p>
                </mbutton>
              </div>
            </box-model>
          </div>
        <?php

        } else
          foreach ($FavoriteBeatmaps as $Feedback) {
            $Beatmap = $Feedback->reference;

            include TEMPLATE . "/components/beatmaps/_beatmap.php";
          }

        ?>
      </div>

      <?php

      /**
       * @var ?Score
       */
      $Scores = $CurrentUser->scores()
        ->orderBy("id", "DESC")
        ->groupBy("map_md5")
        ->limit(6)
        ->get();

      /**
       * @var int
       */
      $has_scores = $Scores->count();

      if ($has_scores)
        include __DIR__ . "/beatmaps/_most_played_user.php";

      ?>
    </div>
  </div>
</feed-section>