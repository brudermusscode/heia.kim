<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Model\Beatmap;
use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * Fetch settings
 */
$mode   = filter_input(INPUT_GET, "mode", FILTER_SANITIZE_SPECIAL_CHARS);
$status = filter_input(INPUT_GET, "status", FILTER_SANITIZE_SPECIAL_CHARS);
$order  = filter_input(INPUT_GET, "order", FILTER_SANITIZE_SPECIAL_CHARS);
$filter = filter_input(INPUT_GET, "filter", FILTER_SANITIZE_SPECIAL_CHARS);
$query  = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Limit & offset
 */
$limit  = filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT) ?? 60;
$offset = filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT) ?? 0;

/**
 * Validate limit
 */
if ($limit > 60 && $limit < 1)
  $limit = 60;

/**
 * Validate offset
 */
if ($offset % $limit !== 0)
  $offset = 0;

/**
 * Fetch Sets
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

/**
 * Beatmaps empty?
 * * Success
 */

// TODO: Add infinite scroll end notice.

if (!$BeatmapSets->count())
  exit($Request->success("No Beatmaps found."));

/**
 * Begin the output buffer.
 */
ob_start();


/**
 * Show limited amount of maps if the user is not logged.
 */
if (!LOGGED && $offset > $limit && !USER_COMEBACK) :

  /**
   * Include the sign up call to action banner.
   */
  include SIGN_UP_NOW;

  /**
   * Tell the frontend the end of the infinite scrolling has been reached.
   */
  echo <<<HTML
    <infinite-scroll-full-end-reached></infinite-scroll-full-end-reached>
  HTML;

else:

  /**
   * @var bool
   */
  $include_all_diffs = true;

  foreach ($BeatmapSets as $key => $Set)
    include TEMPLATE . "/beatmapset/_set-card.php";

endif;

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
