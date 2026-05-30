<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\Connect\ConnectGoogleController;

$return = (new ConnectGoogleController)->remove($_POST);

exit(json_encode($return));
