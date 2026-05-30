<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\Restriction\RestrictionAppealsController;

$return = (new RestrictionAppealsController)->edit($_POST);

exit(json_encode($return));
