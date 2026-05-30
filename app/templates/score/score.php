<?php

use Bruder\Heiakim\Model\Score;

$id  = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
$sub = filter_input(INPUT_GET, "sub", FILTER_SANITIZE_SPECIAL_CHARS) ?? "overview";

/**
 * @var Score
 */
$Score = Score::with("user")
  ->with("comments")
  ->with("beatmap")
  ->find($id);

/**
 * User doesn't exist?
 */
if (!$Score)
  include UNAVAILABLE;
else {

  /**
   * @var int
   */
  $show_comments = 4;
  $more_comments_limit = 10;
  $more_comments_offset = $show_comments;

  /**
   * @var Beatmap
   */
  $Beatmap = $Score->beatmap;

?>



  <header score-hdr scroll-manipulated>
    <div class="cover">
      <picture>
        <?php $Beatmap->cover(true); ?>
      </picture>
    </div>

    <div class="header_squad__inr">
      <div fl gap=smol>
        <div circled style=height:4.2em;width:4.2em; score-grade="">
        </div>
      </div>
    </div>
  </header>

  <div class=score-content content-width=smol mt=wide fl fldircol gap=smol+>
    <div fl fldircol gap=smol>
      <?php

      /**
       * Variables can be cleaned up here.
       */
      $cleanup_variables = false;

      include __DIR__ . "/_score.php"; ?>
    </div>

    <!--- COMMENTS --->
    <div>
      <div data-react="comments:create" data-more="comments" data-id=<?= $Score->id; ?> fl fldircol gap=smoler>
        <?php

        /**
         * @var Comment
         */
        $Comments = $Score->comments()
          ->with("user")
          ->orderBy("created_at", "DESC")
          ->limit($show_comments)
          ->get();

        if ($Comments->count()) {

        ?>

          <?php

          foreach ($Comments as $Comment) {
            /**
             * @var User
             */
            $User = $Comment->user;
            include TEMPLATE . "/comments/_comment.php";
          }

          ?>
        <?php } ?>
      </div>
    </div>

    <div>
      <div fl jucc gap=smol alic>
        <form data-form="comments:create,view">
          <input type=hidden name=id value=<?= $Score->id; ?> />
          <input type=hidden name=type value=score />
          <mbutton submit-closest outlined has-icon=left material>
            <i class="mi">add</i>
            <p text smol bold><?= __("Add comment") ?></p>
          </mbutton>
        </form>

        <?php if ($Comments->count() > $show_comments) { ?>
          <div circled has-tooltip=bottom>
            <form data-form="comments:fetch">
              <input type=hidden name=id value=<?= $Score->id; ?> />
              <input type=hidden name=type value=score />
              <input type=hidden name=limit value=<?= $more_comments_limit; ?> />
              <input type=hidden name=offset value=<?= $more_comments_offset; ?> />
              <mbutton submit-closest ripple-effect filled=darker icon-only size="smoler" has-icon material>
                <i class="mi">unfold_more</i>
              </mbutton>
            </form>
            <div ttooltip>
              <p text std bold><?= __("More comments") ?></p>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>

<?php

}

include TEMPLATE . "/global/_scroll_end_logo.php";
