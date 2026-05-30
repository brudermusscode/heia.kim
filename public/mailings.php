<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/vendor/autoload.php";
require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/define.php";

use Bruder\Heiakim\Controller\Mailings\MailingsController;

/**
 * GET Params.
 */
$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Update the mailing by the token.
 */
$edit = (new MailingsController)->edit($_GET);

/**
 * return the user to the homepage.
 */
header("location: /home");