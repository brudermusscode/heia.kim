<?php

use Bruder\Heiakim\Controller\SquadsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SquadsController($_POST))->update();

exit($Controller);
