<?php

use Bruder\Heiakim\Controller\SessionsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SessionsController($_POST))->create();

exit($Controller);
