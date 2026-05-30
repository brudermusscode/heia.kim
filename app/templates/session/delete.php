<?php

use Bruder\Http\Request;
use Bruder\Heiakim\Controller\SessionsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SessionsController($_POST))->delete();

exit($Controller);
