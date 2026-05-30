<?php

use Bruder\Heiakim\Controller\UsersController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new UsersController($_POST))->update();

exit($Controller);
