<?php

use Bruder\Heiakim\Controller\Squad\SquadPostsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SquadPostsController($_POST))->update();

exit($Controller);
