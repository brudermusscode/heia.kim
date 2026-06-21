<?php

use Heiakim\Model\Squad;

$id = aglobal("id");
$page = aglobal("page");
$sub = aglobal("mode_o_sub") ?? "osu";

/**
 * @var ?Squad
 */
$Squad = Squad::find($id);

redirect_unauthorized(resource: $Squad);

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

include __DIR__ . "/_page-navigator.php";
include __DIR__ . "/_mode-menu.php";
include __DIR__ . "/_header.php";
include __DIR__ . "/pages/_$page.php";

include TEMPLATE . "/global/_scroll_end_logo.php";
