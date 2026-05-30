<?php

use Bruder\Heiakim\Model\Squad;

/**
 * Get Parameter.
 */
$sub    = filter_var(get("sub") ?? "index", FILTER_SANITIZE_SPECIAL_CHARS);
$action = filter_var(get("action"), FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var Squad
 */
$Squad = $CurrentUser->squad;

/**
 * @var string
 */
$base_url = "/manage/squad";

if (!$Squad)
  include UNAVAILABLE;
else {

  $squad_headline = $CurrentUser->squad->headline ?? "default.jpg";

  /**
   * SquadUser
   */
  $SquadUser = $CurrentUser->squad_user;

  /**
   * Header
   */
  include TEMPLATE . "/my/_header.php";

  /**
   * File exists for sub?
   */
  $path = TEMPLATE . "/squad/manage/page/_$sub.php";
  $file_exists = file_exists($path);

  include $file_exists ? $path : TEMPLATE . "/squad/manage/page/_index.php";
}

include TEMPLATE . "/global/_scroll_end_logo.php";
