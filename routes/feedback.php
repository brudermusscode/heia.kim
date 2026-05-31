<?php

use Heiakim\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /feedback
 */
$Router->post("/feedback/create", "feedback/create", return: "JSON");
