<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\SessionsController;

$return = (new SessionsController)->remove($_POST);

exit(json_encode($return));