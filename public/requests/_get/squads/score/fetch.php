<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Gamemode;
use Heiakim\Model\Score;
use Heiakim\Model\Squad;
use Heiakim\Http\Request;

/**
 * @var Request $Request
 */

/**
 * Fetch settings
 */
$id     = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
$mode   = filter_input(INPUT_GET, "mode", FILTER_SANITIZE_SPECIAL_CHARS);
$status = filter_input(INPUT_GET, "status", FILTER_SANITIZE_SPECIAL_CHARS);
$limit  = filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT) ?? 10;
$offset = filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var Squad
 */
$Squad = Squad::find($id);

/**
 * Squad exists?
 * ! Error
 */
if (!$Squad)
  exit($Request->error());

/**
 * Validate limit
 */
if ($limit > 10 && $limit < 1)
  $limit = 10;

/**
 * Validate offset
 */
if ($offset % $limit !== 0)
  $offset = 0;

/**
 * @var array
 */
$status = explode(",", $status);

foreach ($status as $key => $s)
  if (!in_array($s, [1, 2]))
    unset($status[$key]);

/**
 * no status set?
 */
if (!$status)
  $status = [2];

/**
 * Mode with mods
 * @var Mode
 */
$Modes = $Squad->active_modes;

/**
 * @var array
 */
$active_modes = [];
foreach ($Modes as $Mode)
  array_push($active_modes, $Mode->mode);

/**
 * Requested mode is active in the squad?
 * ! Error
 */
if (!in_array($mode, $active_modes))
  exit($Request->error());

/**
 * @var array
 */
$mode_with_mods = Gamemode::mods_int_per_mode($mode);

/**
 * @var ?Score
 */
$Scores = $Squad->view(modes: $mode_with_mods, status: [2], limit: $limit, offset: $offset);

/**
 * No more scores to fetch?
 * * Success
 */
if (!$Scores->count())
  exit($Request->success("No scores found."));

/**
 * Begin the output buffer.
 */
ob_start();

foreach ($Scores as $Score)
  include TEMPLATE . "/squads/squad/_score.php";

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
