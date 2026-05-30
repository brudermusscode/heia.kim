<?php

use Bruder\Heiakim\Controller\PasswordResetsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new PasswordResetsController($_POST))->create();

exit($Controller);
