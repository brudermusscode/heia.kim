<?php

use Heiakim\Application\Router;

/**
 * @var Router $Router
 */

$Router->post("/feedback/create", "feedback/create", return: "JSON");
$Router->post("/feedback/delete", "feedback/delete", return: "JSON");
