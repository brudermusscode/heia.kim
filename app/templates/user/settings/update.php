<?php

use Bruder\Heiakim\Controller\User\SettingsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SettingsController($_POST))->update();

exit($Controller);
