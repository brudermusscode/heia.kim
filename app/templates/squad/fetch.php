<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Squad;
use Heiakim\Http\Request;

/**
 * @var Request $Request
 */

/**
 * Fetch settings
 */
$joinable = filter_input(INPUT_GET, "joinable", FILTER_SANITIZE_SPECIAL_CHARS);
$mode   = filter_input(INPUT_GET, "mode", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Limit & offset
 */
$limit  = filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT) ?? 23;
$offset = filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT) ?? 0;

/**
 * Validate limit
 */
if ($limit > 23 && $limit < 1)
  $limit = 23;

/**
 * Validate offset
 */
if ($offset % $limit !== 0)
  $offset = 0;

/**
 * @var Squad
 */
$Squads = Squad::with("members.user")
  ->withCount('members')
  ->when($joinable !== null, function ($query) use ($joinable) {
    $query->where("joinable", $joinable);
  })
  ->when($mode, function ($q) use ($mode) {
    $q->whereJsonContains('modes->' . $mode, 1); // chillig.
  })
  ->orderByDesc("members_count")
  ->limit($limit)
  ->offset($offset)
  ->orderByDesc("created_at")
  ->get();

/**
 * Begin the output buffer.
 */
ob_start();

/**
 * No Squads found?
 * * Success
 */
if (!$Squads->count()) :
  echo "<infinite-scroll-full-end-reached></infinite-scroll-full-end-reached>";

else :

  $include_all_diffs = true;

  foreach ($Squads as $key => $Squad)
    include TEMPLATE . "/squad/_squad.php";

endif;

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
