<?php

use Heiakim\Model\Squad;
use Heiakim\Model\Gamemode;
use Heiakim\Model\User;

/**
 * @var int
 */
$id = filter_var(GET->id ?? 0, FILTER_VALIDATE_INT);

/**
 * @var ?Squad
 */
$Squad = Squad::find($id);

/**
 * @var string
 */
$page = $mode = filter_var(GET->feed ?? "index", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?string
 */
$sub  = filter_var(GET->sub ?? null, FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var array
 */
$pages = [
  "index",
  "osu",
  "ctb",
  "taiko",
  "mania",
  "community",
  "threads",
  "thread",
];

/**
 * @var array
 */
$only_member_pages = [
  "threads",
  "thread",
];

if (!$Squad)
  include UNAVAILABLE;
else {

  /**
   * Fallback to index if the page is invalid.
   */
  if (!in_array($page, $pages))
    $page = "index";

  /**
   * Set the page to `scores` if it is set to any mode. The page
   * partial will handle the rest. Love!
   */
  if (in_array($page, Gamemode::$modes_text)) {
    $mode = $page;
    $mod = in_array($sub, Gamemode::$mods_text) ? $sub : Gamemode::$mods_text[0];
    $gumode = Gamemode::find_gumode($mode, $mod);

    /**
     * @var string
     */
    $page = "scores";
  }

  /**
   * @var string
   */
  $base_url = "/squad/$Squad->id";

  /**
   * Include the header.
   */
  include __DIR__ . "/_header.php";

  /**
   * Include the corresponding page partial.
   */
  include __DIR__ . "/squad/pages/_$page.php";
}

/**
 * Include the footer.
 */
include TEMPLATE . "/global/_scroll_end_logo.php";
