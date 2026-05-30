<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\AuthenticationsController;

/**
 * @var AuthenticationsController
 */
$Controller = new AuthenticationsController($_POST);

exit($Controller->create());
