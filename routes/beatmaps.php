<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /beatmap-set
 */
$Router->get(
  "/beatmap-set/:set_id/:map_id/:mode/:mod",
  "beatmapset/show",
  constraints: [
    "set_id" => "\d+",
    "map_id" => "\d+",
  ],
  title: "Set of Beatmaps"
);
$Router->get(
  "/beatmap-set/:set_id/:map_id",
  "beatmapset/show",
  constraints: [
    "set_id" => "\d+",
    "map_id" => "\d+",
  ],
  title: "Set of Beatmaps"
);

/**
 * @route /beatmap
 */
$Router->get("/beatmap/fetch", "beatmap/fetch", return: "JSON");

/**
 * @route /beatmaps
 */
$Router->get("/beatmaps", "beatmap/index", title: "Beatmaps | " . APP_NAME);
$Router->get("/beatmaps/:mode", "beatmap/index", title: "Beatmaps | " . APP_NAME);
$Router->get("/beatmaps/:mode/:status", "beatmap/index", title: "Beatmaps | " . APP_NAME);
$Router->get("/beatmaps/:mode/:status/:order", "beatmap/index", title: "Beatmaps | " . APP_NAME);
$Router->get("/beatmaps/:mode/:status/:order/:filter", "beatmap/index", title: "Beatmaps | " . APP_NAME);

/**
 * @route /request
 */
$Router->get("/request/new", return: JSON);
$Router->post("/request/create", return: JSON);
