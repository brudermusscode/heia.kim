<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\Mailings\MailingsController;

$return = (new MailingsController)->edit($_POST);

exit(json_encode($return));
