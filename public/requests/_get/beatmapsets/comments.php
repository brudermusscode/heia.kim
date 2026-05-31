<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Comment;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var Beatmap\Set
 */
$Set = Beatmap\Set::with("comments.user")->find($id);

/**
 * @var Comment
 */
$Comments = $Set->comments;

/**
 * Begin output buffer
 */
ob_start();

?>

<div comments-container filled class="s__comments">
  <div dragme></div>
  <div class=comments get-scroll>
    <div get-height fl fldircol gap=smol pblock14 data-react="comments:create">
      <?php

      if (!$Comments->count()) {

      ?>

        <box-model empty>
          <bm-inr size=wide>
            <div tac fl fldircol gap=smol+>
              <p>
                <i class="mi" size=wide>forum</i>
              </p>
              <p text std bold><?= __("No comments") ?></p>
            </div>
          </bm-inr>
        </box-model>

      <?php

      } else
        foreach ($Comments as $Comment)
          include TEMPLATE . "/components/comments/_comment.php";

      ?>

    </div>
  </div>

  <div composer class="composer">
    <form data-form="comments:create">
      <div class="textarea">
        <input type=hidden name=type value=beatmap />
        <input type=hidden name=reference_id value=<?= $id; ?> />
        <textarea enter-submitable rows="1" name=comment_string auto-resize placeholder="Write a comment..."></textarea>
        <div>
          <mbutton submit-closest material icon-only filled=darker size=smol clickable>
            <div>
              <p text std>
                <i class="mi">send</i>
              </p>
            </div>
          </mbutton>
        </div>
      </div>
    </form>
  </div>
</div>

<?php

die($Request->success(data: ob_get_clean()));
