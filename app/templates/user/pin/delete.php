<?php

use Bruder\Heiakim\Controller\User\PinsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new PinsController($_POST))->delete();

exit($Controller);
