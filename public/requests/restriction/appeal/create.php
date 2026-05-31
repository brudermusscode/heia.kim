<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Controller\Controller\Restriction\RestrictionAppealsController;

$return = (new RestrictionAppealsController)->create($_POST);

exit(json_encode($return));
