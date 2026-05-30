<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\CommentsController;

$return = (new CommentsController)->create($_POST);

exit(json_encode($return));