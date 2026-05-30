<?php

use Bruder\Heiakim\Controller\ReportsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new ReportsController($_POST))->create();

exit($Controller);
