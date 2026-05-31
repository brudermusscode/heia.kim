<?php

use Heiakim\Application\Router;

/**
 * @var Router $Router
 */

$Router->get("/legal/:sub", "legal/index", title: "Legal section on " . APP_NAME);
$Router->get("/legal/:sub/:action", "legal/index", title: "Legal section on " . APP_NAME);
