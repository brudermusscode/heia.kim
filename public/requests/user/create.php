<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\UsersController;

/**
 * @var UsersController
 */
$Controller = new UsersController($_POST);

exit($Controller->create());
