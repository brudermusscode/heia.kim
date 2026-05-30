<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Heiakim\Controller\Squad\SquadPostVotesController;

$return = (new SquadPostVotesController)->create($_POST);

exit(json_encode($return));
