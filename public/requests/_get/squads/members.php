<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Http\Request;
use Heiakim\Time\Time;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$query =   filter_input(INPUT_POST, "query", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var int
 */
$clan_id = filter_input(INPUT_POST, "clan_id", FILTER_SANITIZE_NUMBER_INT)
  ?? $CurrentUser->squad
  ? $CurrentUser->squad->id
  : 0;

/**
 * Logged?
 */
if (!VERIFIED)
  exit($Request->error("!UNVERIFIED"));

/**
 * @var Squad
 */
$Squad = Squad::find($clan_id);

/**
 * @var SquadUser
 */
$Members = $Squad->members()
  ->whereHas("user", function ($q) use ($query) {
    $q->where("name", "LIKE", "%$query%");
  })
  ->with("user", function ($q) use ($query) {
    $q->where("name", "LIKE", "%$query%");
  })
  ->get();

/**
 * Begin the output buffer.
 */
ob_start();

if ($Members->count()) {
  foreach ($Members as $Member) {
    $User = $Member->user;
    $Privileges = $Member->privileges()[0];

?>

    <div rounded hoverable animation=fade-in option data-id="<?= $User->id; ?>" data-name="<?= $User->name; ?>">
      <div fl gap=smol+ alic posrel pblock12 pinline6>
        <picture size=smol circled>
          <?php $User->image(); ?>
        </picture>
        <div fl fldircol gap=smolest>
          <p text std bold><?= $User->name; ?></p>
          <p text smol timestamp>
            <strong><?= $Privileges->get_display()->name; ?></strong> <?= __("for") ?>
            <?= Time::ago($Member->created_at); ?>
          </p>
        </div>
      </div>
    </div>

<?php

  }
} else
  echo <<<TEXT
  <div fl fldircol gap=smol align-items=center mt mb animation=fade-in>
    <i class="mi" size=wide>person_search</i>
    <p text std bold>No member found</p>
  </div>
  TEXT;

die($Request->success(data: ob_get_clean()));
