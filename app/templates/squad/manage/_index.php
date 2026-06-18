<?php

use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

# Router parameter.
$sub    = aglobal("sub") ?? "index";
$action = aglobal("action");

/**
 * @var Squad
 */
$Squad = CurrentUser->squad;

if (!$Squad)
  include UNAVAILABLE;
else {

  $base_url = "/manage/squad";

  $squad_headline = CurrentUser->squad->headline ?? "default.jpg";

  /**
   * @var SquadUser
   */
  $SquadUser = CurrentUser->squad_user;

  # Partial inclusion.
  include TEMPLATE . "/my/_header.php";

  $path = TEMPLATE . "/squad/manage/page/_$sub.php";
  $file_exists = file_exists($path);

  include $file_exists ? $path : TEMPLATE . "/squad/manage/page/_index.php";
}

include TEMPLATE . "/global/_scroll_end_logo.php";
