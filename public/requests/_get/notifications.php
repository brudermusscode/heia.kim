<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$category = filter_input(INPUT_GET, "category", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * User logged?
 */
if (!LOGGED)
  exit($Request->error("!NOT_LOGGED"));

/**
 * Update notifications checked.
 */
$CurrentUser->settings
  ->update([
    "checked_notifications_at" => CURRENT_TIMESTAMP,
  ]);

/**
 * Start output buffer
 */
ob_start();

/**
 * Include the template.
 */
include COMPONENT . "/notifications/index.php";

die($Request->success(data: ob_get_clean()));
