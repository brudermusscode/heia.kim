<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Model\User;
use Bruder\Http\Request;
use Bruder\Heiakim\Model\Change;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$name = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Name is set?
 * ! Error
 */
if (!$name)
  exit($Request->error());

/**
 * User logged and the name is not a former name of the current user?
 */
if (LOGGED) {

  /**
   * @var ?Change
   */
  $NameChange = $CurrentUser->name_changes()
    ->where("previous_value", $name)
    ->orWhere("updated_value", $name)
    ->first();

  /**
   * Name is a former name of the current user?
   * ! Error
   */
  if ($NameChange)
    exit($Request->error("<strong>This name is already taken.</strong>"));
}

/**
 * Name is valid in any case?
 */
(new User)->validate_name($name);

/**
 * * Success
 */
die($Request->success("OK!"));
