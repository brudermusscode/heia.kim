<?php

use Bruder\Heiakim\Controller\ReactionsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new ReactionsController($_POST))->create();

exit($Controller);
