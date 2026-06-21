<?php

use Heiakim\Model\Score;
use Heiakim\Model\Beatmap;
use Illuminate\Support\Collection;

/**
 * @var ?Beatmap
 */
$FavoriteBeatmaps = CurrentUser->favorite_beatmaps;

?>

<div grid-repeat gap=smol>
  <?php if (!$FavoriteBeatmaps->count()) : ?>
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
  <?php else :
    foreach ($FavoriteBeatmaps as $Feedback) :
      $Beatmap = $Feedback->reference;
      include TEMPLATE . "/beatmap/_beatmap-row.php";
    endforeach;
  endif; ?>
</div>

<?php

/**
 * @var Collection<Score>
 */
$Scores = CurrentUser->scores()
  ->join("maps", "scores.map_md5", "=", "maps.md5")
  ->join("mapsets", "maps.set_id", "=", "mapsets.id")
  ->whereIn("scores.status", [2])
  ->orderBy("scores.id", "DESC")
  ->groupBy("mapsets.id")
  ->limit(6)
  ->get();

if ($Scores->count()) : ?>
  <div fl fldircol gap=smol+>
    <div title-inline fl alic gap=smol+>
      <mbutton midler outlined icon-only no-hover>
        <mi>favorite</mi>
      </mbutton>
      <div>
        <p text midler bold>Like these?</p>
        <p text smol color=company>Some beatmaps based on what you play alot!</p>
      </div>
    </div>

    <div grid-repeat gap=smol>
      <?php foreach ($Scores as $Score) :
        $Beatmap = $Score->beatmap;
        include TEMPLATE . "/beatmap/_beatmap-row.php";
      endforeach; ?>
    </div>
  </div>
<?php endif; ?>