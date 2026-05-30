<?php

use Bruder\Heiakim\Controller\Squad\SquadPostPollAnswersController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SquadPostPollAnswersController($_POST))->create();

exit($Controller);
