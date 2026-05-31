<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\Connect\ConnectOsuController;

$return = (new ConnectOsuController)->create($_POST);

exit(json_encode($return));
