<?php

use Heiakim\Application\Logger;
use Heiakim\Application\Router;
use Heiakim\Utils\Arr;

/**
 * Maintenance mode.
 */
// if (IS_MAINTENANCE)
//   header("location: /maintenance");

/**
 * @var Router
 */
$Router = new Router;

/**
 * Populates the routes array in the router by iterating through
 * all files in the routes/ directory.
 */
foreach (glob(ROOT . "/routes/*.php") as $filename)
  include $filename;

/**
 * The requested uri and fallback to home if it is the
 * root page.
 */
$_REQUEST_URI = (
  !str_starts_with($_SERVER["REQUEST_URI"] ?? "", "/")
  ? "/"
  : ""
) . $_SERVER["REQUEST_URI"] ?? "";

/**
 * @var string
 */
$uri = parse_url($_REQUEST_URI)['path'];
$request_method = $_SERVER["REQUEST_METHOD"];

/**
 * Start the routing for the current requested path.
 *
 * @var array
 */
try {
  $_CURRENT_ROUTE = $Router->route(
    uri: $uri,
    method: $request_method,
  )->current_route;
} catch (\Exception $e) {
  Logger::to_file($e);

  redirect("/home");
}

/**
 * Creates a shorthand for the GET super-global and converts it to
 * an object to make anything in it accessible via the arrow
 * syntax. Easier m8.
 */
define("GET", Arr::objectify($_GET));

/**
 * Set some global definitions.
 */
define("CURRENT_PAGE", $_CURRENT_ROUTE["page"]);
define("CURRENT_PAGE_TITLE", $_CURRENT_ROUTE["title"]);

/**
 * Is in editor mode?
 */
define("IS_EDIT_MODE", CURRENT_PAGE === "editor");

/**
 * Begin the output buffer.
 */
ob_start();

/**
 * Include the title, only if one is set. This way it's skipping
 * on as certain as all POST requests.
 */
if ($_CURRENT_ROUTE["title"] && !isset($_GET["is_popup"]))
  echo "<title style='display:none;'>" . $_CURRENT_ROUTE["title"] . "</title>";

/**
 * Require the returned template from the Router.
 */
include TEMPLATE . "/" . $_CURRENT_ROUTE["template"] . ".php";

/**
 * Get the output from the buffer and clean.
 */
$_INCLUDE_TEMPLATE = ob_get_clean();

/**
 * Die if the request is made from with ajax, so that we just
 * render the template in the end.
 */
if ($_SERVER["HTTP_X_REQUESTED_WITH"] ?? false === "XMLHttpRequest")
  die($_INCLUDE_TEMPLATE);

/**
 * The request neither came from a browser or an ajax script?
 */
else if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]))
  die("Invalid request");
