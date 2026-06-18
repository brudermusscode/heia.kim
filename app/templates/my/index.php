<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var ?Squad $CurrentSquad
 * @var ?SquadUser $CurrentSquadUser
 */

authorize(resource: CurrentUser);

# Serialize GET parameter.
$category = aglobal("category") ?? "overview";
$sub      = aglobal("sub");
$var      = aglobal("var");

ob_start(); ?>

<div content-width=std fl fldircol <?= $sub ? "pt42 gap" : "content-gap" ?>
  data-action="user-manager:category">

  <?php

  include __DIR__ . "/_header.php";

  $sub_path = __DIR__ . "/pages/$category/_$sub.php";
  $file_path = __DIR__ . "/pages/$category/_index.php";
  $overview_path = __DIR__ . "/pages/overview/_index.php";

  include !$sub && file_exists($file_path)
    ? $file_path
    : (
      $sub && file_exists($sub_path) ? $sub_path : $overview_path
    ); ?>
</div>

<?php die(success(data: ob_get_clean()));
