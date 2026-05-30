<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\ImagesController;

$return = (new ImagesController)->remove($_POST);

exit(json_encode($return));
