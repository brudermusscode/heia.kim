<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Comment;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * User verified?
 */
if (!VERIFIED)
  exit($Request->error("!UNVERIFIED"));

/**
 * Validate reference object.
 */
$Reference = Comment::validate_reference_with_type($type, $id);

/**
 * Reference exists?
 */
if (!$Reference)
  exit($Request->error());

/**
 * Create every possibly needed variable for includes.
 */
$Score = $Comment = $Reference;

ob_start();

?>


<box-model background=invert color=invert elevated=wide composer>
  <bm-inr size=std>
    <form data-form="comments:create" posrel fl fldircol gap>
      <input type=hidden name=id value=<?= $Score->id; ?> />
      <input type=hidden name=type value=<?= $type; ?> />
      <div fl fldircol gap=smol+>
        <div fl gap=smol+ alic>
          <div fl gap=smol alic>
            <picture circled style=height:1.6em;width:1.6em;>
              <?php $CurrentUser->image(); ?>
            </picture>
            <div fl gap=smol alic>
              <p text std><?= __("Post as") ?> <strong><?= $CurrentUser->name; ?></strong></p>
            </div>
          </div>
          <p text std>&middot;</p>
          <div fl gap=smol alic>
            <i class="mi" size=std>public</i>
            <p text std><?= __("Public") ?></p>
          </div>
        </div>

        <div textarea>
          <textarea autofocus filled=lighter auto-height material name=comment_string placeholder="<?= __("Write a comment"); ?>..."></textarea>
        </div>
      </div>

      <div fl jucend>
        <mbutton filled no-hover-shadow color=dynamic submit-closest>
          <i class=mi size=std>prompt_suggestion</i>
        </mbutton>
      </div>
    </form>
  </bm-inr>
</box-model>

<?php

die($Request->success(data: ob_get_clean()));
