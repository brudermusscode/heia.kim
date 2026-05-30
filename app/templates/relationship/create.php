<?php

use Bruder\Heiakim\Controller\RelationshipsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new RelationshipsController($_POST))->create();

exit($Controller);
