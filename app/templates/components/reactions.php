<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Reaction\ReactionPackage;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$referende_id = filter_input(INPUT_GET, "reference_id", FILTER_VALIDATE_INT);

/**
 * @var string
 */
$reaction_type = filter_input(INPUT_GET, "reaction_type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * User logged?
 * ! Error
 */
if (!LOGGED)
  exit($Request->error("!NOT_LOGGED"));

/**
 * Params valid?
 * ! Error
 */
if (!$referende_id || !$reaction_type)
  exit($Request->error());

/**
 * @var ReactionPackage
 */
$ReactionPackages = ReactionPackage::all();

/**
 * Begin the output buffer.
 */
ob_start();

?>

<div reactions-window animation=material-expand-in-bottom background=invert color=invert elevated=wide class=reactions>
  <form data-form="reactions:create">
    <input type=hidden name=reference_id value=<?= $referende_id; ?> />
    <input type=hidden name=type value=<?= $reaction_type; ?> />
    <input type=hidden name=reaction />
  </form>

  <div pblock38 pinline32 data-action="reactions:create" posrel style="padding-top:0;">
    <?php

    foreach ($ReactionPackages as $key => $Package) {
      /**
       * Transform the name top a displayable string
       */
      $package_name = str_replace("_", " ", $Package->package_name);

    ?>
      <div style="position:sticky;top:0;z-index:<?= $key; ?>;padding-bottom:.4em;padding-top:24px;" background=invert>
        <p text std bold><?= ucwords($package_name); ?></p>
      </div>

      <div mb=smol fl jucstart gap=smolest flex-wrap=wrap>
        <?php foreach ($Package->emojis as $key => $Emoji) { ?>
          <div fl jucc alic data-reaction="<?= $Emoji->reaction; ?>" emoji hoverable>
            <p text mid><?= $Emoji->emoji; ?></p>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</div>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
