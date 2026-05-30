<?php

use Bruder\Heiakim\Controller\Squad\SquadRequestsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SquadRequestsController($_POST))->create();

exit($Controller);
