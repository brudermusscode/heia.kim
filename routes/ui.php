<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /ui
 */
$Router->get("/ui/unavailable",  "components/unavailable",  return: "JSON");
$Router->get("/ui/user-menu",  "components/user-menu",  return: "JSON");
$Router->get("/ui/user-manager",  "components/user-manager",  return: "JSON");
$Router->get("/ui/reactions",  "components/reactions",  return: "JSON");
$Router->get("/ui/squad/posting-machine",  "components/squad/posting-machine",  return: "JSON");
