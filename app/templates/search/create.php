<?php

use Bruder\Http\Request;
use Bruder\Heiakim\Controller\SearchesController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new SearchesController($_POST))->create();

exit($Controller);
