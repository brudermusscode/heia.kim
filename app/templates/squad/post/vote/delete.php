<?php

use Bruder\Heiakim\Controller\Squad\SquadPostVotesController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SquadPostVotesController($_POST))->delete();

exit($Controller);
