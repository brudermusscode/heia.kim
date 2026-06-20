<?php

use Heiakim\Model\Artist;

/**
 * @var int
 */
$id = filter_var($_GET["id"] ?? 0, FILTER_VALIDATE_INT);

/**
 * @var string
 */
$query  = filter_var($_GET["query"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var int
 */
$fetch_count = 40;

/**
 * @var ?Artist
 */
$Artist = Artist::with(["beatmapsets" => function ($q) use ($fetch_count) {
  $q->limit($fetch_count);
}])
  ->with("feedback")
  ->find($id);

/**
 * Artist exists?
 */
if (!$Artist)
  include UNAVAILABLE;
else {

  /**
   * Beatmap Sets
   */
  $Sets = $Artist->beatmapsets;

  /**
   * Full playcount including all plays of any beatmapset of this artist.
   *
   * @var int
   */
  $artist_play_count = $Artist->play_count();

  include TEMPLATE . "/home/_page-navigator.php";  ?>

  <div class="artists__banner" scroll-manipulated>
    <div class="artists__banner_image">
      <picture>
        <?php $Artist->cover(true); ?>
      </picture>
    </div>

    <div class="artists__banner_inr">
      <div class="artists__banner_info">
        <div class=name>
          <p text bold trimt><?= $Artist->name; ?></p>
        </div>

        <div class="artists__banner_info__bottom">
          <div class=played background=white rounded=wide>
            <p text std>
              <i class="mi">play_circle</i>
            </p>
            <p text std>Played <strong><?= number_format($artist_play_count); ?></strong> times</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <form data-form="artists:scroll" method="POST" playaction>
    <input name=limit value="<?= $fetch_count; ?>" type="hidden" />
    <input name=id value="<?= $Artist->id; ?>" type="hidden" />
    <input data-react="infinite-scroll-offset" name=offset value="<?= $fetch_count; ?>" type="hidden" />

    <button type=submit name=ass>submit</button>
  </form>

  <div class=artists>
    <div class=a__actions title-inline>
      <div fl justify-content=space-between gap=smol>
        <div>&nbsp;</div>
        <div>
          <form data-form="feedback:create">
            <input type="hidden" name="type" value="artist">
            <input type="hidden" name="reference_id" value="<?= $Artist->id; ?>">
            <input type="hidden" name="action" value="thumb_up">
            <div has-count fl gap=smol+ alic>
              <div count>
                <p text std bold><?= $Artist->feedback->count(); ?></p>
              </div>
              <mbutton mid submit-closest icon-only ripple-effect filled=lighter has-tooltip=bottom <?php if (LOGGED && CurrentUser->feedback()->where("type", "artist")->where("reference_id", $Artist->id)->count()) echo "active"; ?>>
                <mi>kid_star</mi>
                <div ttooltip>
                  <p text std bold>Add to favorites</p>
                </div>
              </mbutton>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div pblock12>
      <p text bold midler>Beatmaps</p>
    </div>

    <div class="artists__maps" grid-repeat scroll="infinite">

      <?php

      if (!$Sets->count()) {

      ?>

        <div style="max-width:600px;">
          <box-model outlined mb="std">
            <div pblock48 pinline48>
              <div fl fldircol gap>
                <div fl gap align-items="center">
                  <p text mid>
                    <i class="mi">web_stories</i>
                  </p>
                </div>
                <p text bold midler>Beatmaps</p>
                <p text smol><strong><?= htmlspecialchars_decode($Artist->name); ?></strong> has no beatmaps
                  added or
                  mapped by other players. You can add it to your favorites and get notified if there is any
                  update!</p>
              </div>
            </div>
          </box-model>
        </div>

      <?php

      } else {
        $include_all_diffs = true;

        foreach ($Sets as $key => $Set) {
          if ($key == $fetch_count) break;

          include TEMPLATE . "/beatmapset/_set-card.php";
        }
      }

      ?>
    </div>
  </div>

<?php

  include TEMPLATE . "/global/_scroll_end_logo.php";
}
