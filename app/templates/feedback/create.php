<?php

use Bruder\Http\Request;
use Bruder\Heiakim\Controller\FeedbackController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new FeedbackController($_POST))->create();

exit($Controller);
