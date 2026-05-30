<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /connect
 */
$Router->get("/connect/:type", "connect/index", title: function ($params) {
  return "Connect " . $params["type"] . " to your " . APP_NAME . " account!";
});
$Router->post("/connect/start", return: "JSON");
$Router->post("/connect/create", return: "JSON");
$Router->post("/connect/delete", return: "JSON");
