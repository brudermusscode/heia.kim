<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\User\PinsController;

$return = (new PinsController)->create($_POST);

exit(json_encode($return));
