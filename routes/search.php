<?php

use Heiakim\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /search
 */
$Router->post("/search/create", "search/create", return: "JSON");
