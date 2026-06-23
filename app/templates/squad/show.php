<?php

use Heiakim\Model\Squad;

$id = aglobal("id");
$page = aglobal("page");
$sub = aglobal("mode_o_sub") ?? "osu";
$mod = aglobal("mod");

/**
 * @var ?Squad
 */
$Squad = Squad::find($id);

redirect_unauthorized($Squad);

$pages = [
  "index",
  "scores",
  "community",
  "threads",
  "thread",
];

$only_member_pages = [
  "threads",
  "thread",
];

if (!in_array($page, $pages))
  $page = "index";

$base_url = "/squad/$Squad->id";

/**
 * @var object
 */
$placements = $Squad->placement();

include __DIR__ . "/_page-navigator.php";
include __DIR__ . "/_compose-menu.php";
include __DIR__ . "/_header.php";
include __DIR__ . "/pages/_$page.php";

include TEMPLATE . "/global/_scroll_end_logo.php";
