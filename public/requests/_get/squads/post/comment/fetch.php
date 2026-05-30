<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Application\Application;
use Bruder\Heiakim\Model\Squad\SquadPost;
use Bruder\Heiakim\Model\Squad\SquadPostComment;
use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id     = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var int
 */
$limit  = filter_input(INPUT_GET, "limit", FILTER_VALIDATE_INT) ?? 10;

/**
 * @var int
 */
$offset = filter_input(INPUT_GET, "offset", FILTER_VALIDATE_INT) ?? 0;

/**
 * Validate limit.
 */
if ($limit > 10)
  $limit = 10;

/**
 * @var ?SquadPost
 */
$Post = SquadPost::find($id);

/**
 * Post doesn't exist?
 */
if (!$Post)
  exit($Request->error("<strong>No post found.</strong>"));

/**
 * @var ?SquadPostComment
 */
$Comments = $Post
  ->comments()
  ->orderBy("created_at", "DESC")
  ->limit($limit)
  ->offset($offset)
  ->get();

/**
 * @var bool Whether or not there are more comments left.
 */
$return->end = $Comments->count() < $limit;

/**
 * No comments left?
 * * Success
 */
if (!$Comments->count())
  exit($Request->success("No comments found."));

/**
 * Begin the output buffer.
 */
ob_start();

/**
 * Include all new comments.
 */
foreach ($Comments as $Comment)
  include TEMPLATE . "/squads/squad/post/_comment.php";

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
