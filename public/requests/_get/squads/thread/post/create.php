<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Thread\Thread;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id   = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * User verified?
 * ! Error
 */
if (!VERIFIED)
  exit($Request->error("!UNVERIFIED"));

/**
 * User has squad?
 * ! Error
 */
if (!$CurrentUser->squad)
  exit($Request->error("<strong>You are not in a Squad.</strong>"));

/**
 * @var Squad
 */
$Squad = $CurrentUser->squad;

/**
 * @var Thread
 */
$Thread = Thread::find($id);

/**
 * Thread exists?
 * ! Error
 */
if (!$Thread)
  exit($Request->error());

/**
 * Squads of user and thread are the same?
 * ! Error
 */
if (!$Squad->id === $Thread->squad->id)
  exit($Request->error());

/**
 * Begin output buffer
 */
ob_start();

?>

<box-model background=invert color=invert elevated=wide composer>
  <bm-inr size=std>
    <form data-form="threads:post,create" posrel fl fldircol gap>
      <input type=hidden name=id value=<?= $Thread->id; ?> />
      <div fl fldircol gap=smol+>
        <div fl gap=smol alic>
          <div fl gap=smol alic>
            <picture circled style=height:1.6em;width:1.6em;>
              <?php $CurrentUser->image(); ?>
            </picture>
            <div fl gap=smol alic>
              <p text std trimt><strong><?= $CurrentUser->name; ?></strong></p>
            </div>
          </div>
          <p text std bold>&middot;</p>
          <div fl gap=smoler alic>
            <div background=dynamic rounded="wide" pblock6 pinline2 ttup>
              <p text smol bold color=dynamic><?= $Squad->tag; ?></p>
            </div>
            <p text std bold trimt><?= $Squad->name; ?></p>
          </div>
        </div>

        <div textarea>
          <textarea autofocus filled=lighter auto-height material name=content placeholder="Cool story bro..."></textarea>
        </div>
      </div>

      <div fl jucsb alic>
        <mbutton filled color=dynamic icon-only material has-tooltip=top disabled>
          <i class=mi size=std>attach_file_add</i>
          <div ttooltip>
            <p text std bold><?= __("Add attachments") ?></p>
          </div>
        </mbutton>
        <mbutton filled color=dynamic material submit-closest>
          <i class=mi size=std>prompt_suggestion</i>
        </mbutton>
      </div>
    </form>
  </bm-inr>
</box-model>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
