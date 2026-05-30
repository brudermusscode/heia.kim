<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\Order\OrderPaypalController;

$return = (new OrderPaypalController)->capture($_POST);

exit(json_encode($return));