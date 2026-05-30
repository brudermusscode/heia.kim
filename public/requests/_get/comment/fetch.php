<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Model\Comment;
use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * GET parameter
 */
$id     = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT) ?? 0;
$type   = filter_input(INPUT_POST, "type", FILTER_SANITIZE_SPECIAL_CHARS);
$limit  = filter_input(INPUT_POST, "limit", FILTER_VALIDATE_INT) ?? 10;
$offset = filter_input(INPUT_POST, "offset", FILTER_VALIDATE_INT) ?? 0;

/**
 * Validate the reference with type and id.
 */
$Reference = Comment::validate_reference_with_type($type, $id);

/**
 * Reference exists?
 */
if (!$Reference)
  exit($Request->error());

/**
 * @var Comment
 */
$Comments = $Reference
  ->comments()
  ->where("type", $type)
  ->where("reference_id", $id)
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
  exit($Request->success("No comments left my friend."));

/**
 * Begin output buffer.
 */
ob_start();

/**
 * Include all new comments.
 */
foreach ($Comments as $Comment)
  include TEMPLATE . "/comments/_comment.php";

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
