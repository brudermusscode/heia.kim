<?php

use Bruder\Heiakim\Controller\AuthenticationsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new AuthenticationsController($_POST))->update();

exit($Controller);
