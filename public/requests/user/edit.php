<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\UsersController;

$return = (new UsersController)->edit($_POST);

exit(json_encode($return));
