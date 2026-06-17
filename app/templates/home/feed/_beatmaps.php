<?php

use Heiakim\Model\Score;
use Heiakim\Model\Beatmap;
use Illuminate\Support\Collection;

/**
 * @var ?Beatmap
 */
$FavoriteBeatmaps = CurrentUser->favorite_beatmaps;

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
        <?php if (!$has_favorite_beatmaps) : ?>
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
                  <mbutton mid has-icon=left filled>
                    <mi>explore</mi>
                    <p text bold><?= __("Explore Beatmaps") ?></p>
                  </mbutton>
                </a>
                <mbutton mid data-action="search:open" has-icon=left filled>
                  <mi>search</mi>
                  <p text bold><?= __("Search") ?></p>
                </mbutton>
              </div>
            </box-model>
          </div>
        <?php else :
          foreach ($FavoriteBeatmaps as $Feedback) :
            $Beatmap = $Feedback->reference;
            include TEMPLATE . "/components/beatmaps/_beatmap.php";
          endforeach;
        endif; ?>
      </div>

      <?php

      /**
       * @var Collection<Score>
       */
      $Scores = CurrentUser->scores()
        ->orderBy("id", "DESC")
        ->groupBy("map_md5")
        ->limit(6)
        ->get();

      $has_scores = $Scores->count();

      if ($has_scores) : ?>
        <div fl fldircol gap=smol+>
          <div title-inline fl alic gap=smol+>
            <mbutton mid filled icon-only no-hover>
              <mi mid>favorite</mi>
            </mbutton>
            <p text mid bold>You might like</p>
          </div>

          <div grid-repeat gap=smol>
            <?php foreach ($Scores as $Score) :
              $Beatmap = $Score->beatmap;
              include TEMPLATE . "/components/beatmaps/_beatmap.php";
            endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</feed-section>