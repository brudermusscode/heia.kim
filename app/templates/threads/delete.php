<?php

use Bruder\Heiakim\Controller\Thread\ThreadsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new ThreadsController($_POST))->delete();

exit($Controller);
