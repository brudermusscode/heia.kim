<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\Connect\ConnectGoogleController;

$return = (new ConnectGoogleController)->create($_POST);

exit(json_encode($return));
