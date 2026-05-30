<?php

use Bruder\Heiakim\Controller\RequestsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new RequestsController($_POST))->create();

exit($Controller);
