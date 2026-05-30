<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\SquadsController;

/**
 * @var SquadsController
 */
$Controller = new SquadsController($_POST);

exit($Controller->edit());
