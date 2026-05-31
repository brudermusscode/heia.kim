<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\Connect\ConnectDiscordController;

$return = (new ConnectDiscordController)->auth($_POST);

exit(json_encode($return));
