<?php

/**
 * General routes like the home and error pages.
 */

use Heiakim\Application\Router;

/**
 * @var Router $Router
 */

$Router->get("/not-found", "error/404", title: "Bruder, was geht jetzt?");
$Router->get("/", "home/index", title: APP_NAME . " - Bruder! Geil!");
$Router->get("/home", "home/index", title: APP_NAME . " ~ " . _env("APP_SLOGAN"));
$Router->get("/home/:sub", "home/index", title: APP_NAME . " ~ " . _env("APP_SLOGAN"));
$Router->get("/home/get-content/:file_name", "home/get-content/index", return: "JSON");

$Router->get("/login", "login", title: "Login to " . APP_NAME);
$Router->get("/register", "register", title: "Sign up to " . APP_NAME);

$Router->get("/password-reset", "password-reset/index", title: "Reset password | " . APP_NAME);
$Router->get("/password-reset/:token", "password-reset/index", title: "Verify password reset | " . APP_NAME);
$Router->post("/password-reset/create", "password-reset/create", return: "JSON");
$Router->post("/password-reset/update", "password-reset/update", return: "JSON");

$Router->post("/session/create", "session/create", return: "JSON");
$Router->post("/session/delete", "session/delete", return: "JSON");

$Router->get("/begin/:token", "begin/index", title: "Begin your journey on " . APP_NAME);

$Router->get("/download", "download/index", title: "The easiest to setup with " . APP_NAME);



// rewrite ^/register/with/(.*) /yield.php?page=register&sub=with-api&api=$1&$query_string last;
// rewrite ^/login/with/(.*) /yield.php?page=login&sub=with-api&api=$1&$query_string last;
