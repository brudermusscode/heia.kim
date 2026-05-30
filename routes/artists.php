<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

/**
 * @route /artists
 */
$Router->get("/artists",  "artist/index",  title: "Artist");

/**
 * @route /artist
 */
$Router->get(
  "/artist/:id",
  "artist/show",
  constraints: [
    "id" => "\d+",
  ],
  title: "Artist"
);
$Router->get("/artist/set/fetch",  "artist/set/fetch",  return: "JSON");
