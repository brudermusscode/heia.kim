<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var ?Squad $CurrentSquad
 * @var ?SquadUser $CurrentSquadUser
 */

authorize(resource: CurrentUser);

$category = filter_var(get("category"), FILTER_SANITIZE_SPECIAL_CHARS);
$sub      = filter_var(get("sub"), FILTER_SANITIZE_SPECIAL_CHARS);
$var      = filter_var(get("var"), FILTER_SANITIZE_SPECIAL_CHARS);

# Begin output buffering.
ob_start(); ?>

<div content-width=std fl fldircol data-action="user-manager:category" <?= $sub ? "pt42 gap" : "content-gap" ?>>

  <?php

  include __DIR__ . "/_header.php";

  $overview_path = __DIR__ . "/pages/overview/_index.php";
  $file_path = __DIR__ . "/pages/$category/_index.php";

  if (file_exists($file_path)) {
    if (!$sub) {
      include file_exists($file_path) ? $file_path : $overview_path;
    } else {
      $action_file_path = __DIR__ . "/pages/$category/_$sub.php";
      include file_exists($action_file_path) ? $action_file_path : __DIR__ . "/pages/$category/_index.php";
    }
  } else
    include $overview_path;

  ?>

</div>

<?php

request_success(data: ob_get_clean(), die: true);
