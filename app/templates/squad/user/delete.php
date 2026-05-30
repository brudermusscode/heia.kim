<?php

use Bruder\Heiakim\Controller\Squad\SquadUsersController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SquadUsersController($_POST))->delete();

exit($Controller);
