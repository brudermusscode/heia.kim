<?php

use Heiakim\Application\Router;

/**
 * @var Router $Router
 */

$Router->get("/leaderboard", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:model", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:model/:mode", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:model/:mode/:mod", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:model/:mode/:mod/:type", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:model/:mode/:mode/:type/:ppage", "leaderboard/index", title: "Rankings | " . APP_NAME);
