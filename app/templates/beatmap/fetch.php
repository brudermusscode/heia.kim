<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Beatmap;
use Illuminate\Support\Collection;

$mode   = filter_input(INPUT_GET, "mode", FILTER_SANITIZE_SPECIAL_CHARS);
$status = filter_input(INPUT_GET, "status", FILTER_SANITIZE_SPECIAL_CHARS);
$order  = filter_input(INPUT_GET, "order", FILTER_SANITIZE_SPECIAL_CHARS);
$filter = filter_input(INPUT_GET, "filter", FILTER_SANITIZE_SPECIAL_CHARS);
$query  = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);
$limit  = filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT) ?? 60;
$offset = filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT) ?? 0;

if ($limit > 60 && $limit < 1)
  $limit = 60;

if ($offset % $limit !== 0)
  $offset = 0;

/**
 * @var Collection<Beatmap\Set>
 */
$BeatmapSets = Beatmap\Set::view(
  query: $query,
  filter: $filter,
  status: $status,
  mode: $mode,
  order: $order,
  limit: $limit,
  offset: $offset
);

// TODO: Add infinite scroll end notice.

if (!$BeatmapSets->count())
  exit($Request->success("No Beatmaps found."));

ob_start();

if (!LOGGED && $offset > $limit && !USER_COMEBACK) :

  include SIGN_UP_NOW;
  echo <<<HTML
    <infinite-scroll-full-end-reached></infinite-scroll-full-end-reached>
  HTML;

else:

  $include_all_diffs = true;

  foreach ($BeatmapSets as $key => $Set)
    include TEMPLATE . "/beatmap/_beatmap-row.php";

endif;

die(success(data: ob_get_clean()));
