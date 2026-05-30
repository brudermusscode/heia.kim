<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\PasswordResetsController;

$request = (new PasswordResetsController)->create($_POST);

exit(json_encode($request));