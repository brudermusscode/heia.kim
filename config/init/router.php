<?php

use Heiakim\Application\Logger;
use Heiakim\Application\Router;
use Heiakim\Utils\Arr;

# TODO: Build maintenance mode.

/**
 * @var Router
 */
$Router = new Router;

# Include all routes.
foreach (glob(ROOT . "/routes/*.php") as $filename)
  include $filename;


# Route!
try {
  $_CURRENT_ROUTE = $Router->route(
    uri: parse_url($_SERVER["REQUEST_URI"])['path'],
    method: $_SERVER["REQUEST_METHOD"],
  )->current_route;
} catch (\Throwable $e) {
  Logger::to_file($e);
  redirect("/home");
  exit;
}

# Creates a shorthand for the GET super-global and converts it to an object to make
# anything in it accessible via the arrow syntax. Easier m8.
define("GET", Arr::objectify($_GET));

# Set some global definitions.
define("CURRENT_ROUTE", $_CURRENT_ROUTE);
define("CURRENT_PAGE", CURRENT_ROUTE["page"]);
define("CURRENT_TEMPLATE", CURRENT_ROUTE["template"]);
define("CURRENT_PAGE_TITLE", CURRENT_ROUTE["title"]);
define("IS_EDIT_MODE", CURRENT_PAGE === "editor");

# ! Begin output buffering. ----------
ob_start();

# Title will only be set in GET requests that do not return JSON. It will be set th-
# rough JavaScript.
if (CURRENT_ROUTE["title"] && !isset($_GET["is_popup"]))
  echo "<title style='display:none;'>" . CURRENT_ROUTE["title"] . "</title>";

# Include the returned template from the Router.
include TEMPLATE . (
  CURRENT_TEMPLATE ? "/" . CURRENT_TEMPLATE : substr(CURRENT_ROUTE["uri"], 0, 1)
) . ".php";

# Put the HTML output to this definition.
define("YIELD_OUTPUT", ob_get_clean());
# ! -----------------------------------

# Die here if the request comes from ajax, so we just render the template.
if ($_SERVER["HTTP_X_REQUESTED_WITH"] ?? null === "XMLHttpRequest") {
  header(JSON_RESPONSE);
  die(success(data: [
    "HTML" => YIELD_OUTPUT,
    "params" => $_GET
  ]));
}

# The request neither came from a browser or an ajax script and thus is completly
# scuffed. We die m8.
else if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
  header(JSON_RESPONSE);
  die(error());
}

# When we reach here, we will include the YIELD_OUTPUT to the yield.php in the next
# step. This is, when the app is being initialized through a generic browser call.
