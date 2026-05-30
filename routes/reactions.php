<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /reaction
 */
$Router->post("/reaction/create", "reaction/create", return: JSON);
