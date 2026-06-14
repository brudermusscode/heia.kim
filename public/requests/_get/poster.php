<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var array
 */
$types = [
  "squad_post_comment",
];

/**
 * Type valid?
 */
if (!in_array($type, $types))
  exit($Request->error());


/**
 * User verified?
 */
if (!LOGGED)
  exit($Request->error("!NOT_LOGGED"));

/**
 * User can interact with the community?
 */
if ($CurrentUser->is_socially_excluded())
  exit($Request->error("!SOCIALLY_EXCLUDED"));

/**
 * ? SQUAD POST
 */
if ($type === "squad_post_comment") {
  /**
   * Post exists?
   *
   * @var ?SquadPost
   */
  if (!($Post = SquadPost::find($id)))
    exit($Request->error());

  /**
   * @var Squad
   */
  $Squad = $Post->squad;

  /**
   * @var string
   */
  $form = "squad/post/comment/create";
} else
  exit($Request->error());

/**
 * Begin output buffer.
 */
ob_start();

?>

<box-model background=invert color=invert elevated=wide composer>
  <bm-inr size=std>
    <form request-do="<?= $form; ?>" posrel fl fldircol gap>
      <input type=hidden name=id value=<?= $id; ?> />
      <div fl fldircol gap=smol+>
        <div fl gap=smol+ alic>
          <div fl gap=smol alic>
            <picture circled size=smol>
              <?php $CurrentUser->image(); ?>
            </picture>
            <div fl gap=smol alic>
              <p text std bold><?= $CurrentUser->name; ?></p>
            </div>
          </div>
          <p text std>&middot;</p>
          <div fl gap=smol alic>
            <?php if (isset($Squad)) { ?>
              <mi>vpn_lock</mi>
              <p text std>Restricted</p>
            <?php } else { ?>
              <mi>public</mi>
              <p text std><?= __("Public") ?></p>
            <?php } ?>
          </div>
        </div>

        <div textarea>
          <textarea autofocus filled=lighter auto-height material name=comment_string placeholder="<?= __("Write a comment"); ?>..."></textarea>
        </div>
      </div>

      <div fl jucsb>
        <?php if (isset($Squad)) { ?>
          <div fl gap=smol alic pblock18 rounded=wide background=slight>
            <p text>Post in</p>
            <picture size=smol circled>
              <?php $Squad->logo(); ?>
            </picture>
            <p text std><?= $Squad->name; ?></p>
          </div>
        <?php } ?>

        <mbutton filled no-hover-shadow color=dynamic submit-closest>
          <i class=mi size=std>prompt_suggestion</i>
        </mbutton>
      </div>
    </form>
  </bm-inr>
</box-model>

<?php

die($Request->success(data: ob_get_clean()));
