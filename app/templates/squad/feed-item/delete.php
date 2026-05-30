<?php

use Bruder\Heiakim\Controller\Squad\SquadFeedItemsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SquadFeedItemsController($_POST))->delete();

exit($Controller);
