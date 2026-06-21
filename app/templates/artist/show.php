<?php

use Heiakim\Model\Artist;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Model\Feedback;

$id = filter_var($_GET["id"] ?? 0, FILTER_VALIDATE_INT);
$query  = filter_var($_GET["query"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);
$fetch_count = 40;

/**
 * @var ?Artist
 */
$Artist = Artist::with(["beatmapsets" => function ($q) use ($fetch_count) {
  $q->limit($fetch_count);
}])
  ->with("feedback")
  ->find($id);

redirect_unauthorized($Artist);

/**
 * @var Set
 */
$Sets = $Artist->beatmapsets;

include TEMPLATE . "/home/_page-navigator.php";
include __DIR__ . "/_top-banner.php"; ?>

<form data-form="artists:scroll" method="POST" playaction>
  <input name=limit value="<?= $fetch_count; ?>" type="hidden" />
  <input name=id value="<?= $Artist->id; ?>" type="hidden" />
  <input data-react="infinite-scroll-offset" name=offset value="<?= $fetch_count; ?>" type="hidden" />

  <button type=submit name=ass>submit</button>
</form>

<div class=artist fl fldircol gap=std+>
  <div class=a__actions title-inline>
    <div fl justify-content=space-between gap=smol>
      <mbutton mid has-icon=left active>
        <mi>web_stories</mi>
        Beatmaps
      </mbutton>

      <?php

      /**
       * @var ?Feedback
       */
      $Feedback = CurrentUser->favorite_artists()
        ->where("reference_id",  $Artist->id)
        ->first();

      $likes_this = LOGGED && $Feedback;

      ?>

      <mbutton mid icon-only has-tooltip=bottom filled=lighter
        ripple-effect shadow-submit reload responder=error
        request="feedback:<?= $likes_this ? "delete" : "create" ?>"
        <?php if ($likes_this) : ?>
        data-id="<?= $Feedback->id ?>"
        <?php else : ?>
        data-type="artist"
        data-reference-id="<?= $Artist->id; ?>"
        data-action="thumb_up"
        <?php endif; ?>
        <?= $likes_this ? "active" : "" ?>>
        <mi>bookmark_heart</mi>
        <div ttooltip>
          <p text std bold><?= __(($likes_this ? "Remove from" : "Add to") . " favorites") ?></p>
        </div>
      </mbutton>
    </div>
  </div>

  <div class="artists__maps" grid-repeat scroll="infinite">
    <?php if (!$Sets->count()) : ?>
      <div style="max-width:600px;">
        <box-model outlined mb="std">
          <div pblock48 pinline48>
            <div fl fldircol gap>
              <mi wide>web_stories</mi>
              <p text bold midler>Beatmaps</p>
              <p text smol>
                <strong><?= htmlspecialchars_decode($Artist->name); ?></strong>
                has no beatmaps added or mapped by other players. You can add it to your favorites and get notified if there is any update!
              </p>
            </div>
          </div>
        </box-model>
      </div>
    <?php else :
      $include_all_diffs = true;

      foreach ($Sets as $key => $Set) :
        if ($key == $fetch_count) break;

        include TEMPLATE . "/beatmap/_beatmap-row.php";
      endforeach;
    endif; ?>
  </div>
</div>

<?php include TEMPLATE . "/global/_scroll_end_logo.php";
