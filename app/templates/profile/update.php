<?php

use Bruder\Heiakim\Controller\ProfilesController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new ProfilesController($_POST))->update();

exit($Controller);
