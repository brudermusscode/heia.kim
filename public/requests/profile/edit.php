<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\ProfilesController;

$return = (new ProfilesController)->edit($_POST);

exit(json_encode($return));
