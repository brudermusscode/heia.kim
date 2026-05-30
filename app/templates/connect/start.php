<?php

use Bruder\Heiakim\Controller\Connect\ConnectController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new ConnectController($_POST))->start();

exit($Controller);
