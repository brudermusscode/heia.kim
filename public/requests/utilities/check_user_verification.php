<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * User is logged?
 * ! Error
 */
if (!LOGGED)
  exit($Request->error("!NOT_LOGGED"));

/**
 * User is verified?
 * ! Error
 */
if ($CurrentUser->priv < 3)
  exit($Request->error("!UNVERIFIED"));

/**
 * * Success
 */
die($Request->success("OK!"));
