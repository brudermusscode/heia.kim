<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\AuthenticationsController;

$return = (new AuthenticationsController)->validate($_POST);

exit(json_encode($return));
