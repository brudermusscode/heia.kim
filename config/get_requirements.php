<?php

require_once __DIR__ . "/init.php";

use Heiakim\Http\Request;

/**
 * @var Request
 */
$Request = new Request;

/**
 * Set header to JSON response
 */
header(JSON_RESPONSE);
