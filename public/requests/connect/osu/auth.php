<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\Connect\ConnectOsuController;

$return = (new ConnectOsuController)->auth($_POST);

exit(json_encode($return));