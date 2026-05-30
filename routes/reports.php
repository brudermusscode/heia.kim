<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /report
 */
$Router->get("/report/new", "report/new", return: JSON);
$Router->post("/report/create", "report/create", return: JSON);
