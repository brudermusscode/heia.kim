<?php

use Bruder\Heiakim\Controller\User\SettingsPrivacyController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SettingsPrivacyController($_POST))->update();

exit($Controller);
