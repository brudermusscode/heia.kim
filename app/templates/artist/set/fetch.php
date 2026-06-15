<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Artist;
use Heiakim\Http\Request;
use Heiakim\Validate\Search;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id     = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var int
 */
$limit  = filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT) ?? 60;

/**
 * @var int
 */
$offset = filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var string
 */
$query  = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);

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
 * @var ?Artist
 */
$Artist = Artist::with(["beatmapsets" => function ($q) use ($limit, $offset, $query) {
  $q = $q->limit($limit)
    ->offset($offset);

  if ($query)
    $q->whereRaw("MATCH(title, artist, version, creator) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)]);
}])
  ->find($id);

/**
 * No artist found?
 * ! Error
 */
if (!$Artist)
  exit($Request->error("No Artist found."));

/**
 * Fetch Sets
 */
$Sets = $Artist->beatmapsets;

if (!$Sets->count())
  exit($Request->error(data: (object) ["end" => true]));

$include_all_diffs = true;

ob_start();

foreach ($Sets as $key => $Set)
  include TEMPLATE . "/beatmapset/_set-card.php";

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
