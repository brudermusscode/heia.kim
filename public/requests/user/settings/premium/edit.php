<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\User\SettingsPremiumController;

$return = (new SettingsPremiumController)->edit($_POST);

exit(json_encode($return));
