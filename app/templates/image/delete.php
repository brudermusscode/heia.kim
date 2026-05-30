<?php

use Bruder\Heiakim\Controller\ImagesController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new ImagesController($_POST))->delete();

exit($Controller);
