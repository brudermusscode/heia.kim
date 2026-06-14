<?php

use Heiakim\Application\Router;
use Heiakim\Model\Gamemode;
use Heiakim\Model\User;

/**
 * @var Router $Router
 */

$Router->get(
  "/u/:id",
  "user/show",
  constraints: [
    "id" => "\d+",
  ],
  title: function ($params) {
    $User = User::find($params["id"]);
    $mode_mod =  Gamemode::convert_gumode_to_mode_mod($User->preferred_mode);

    return $User->name . " × " . Gamemode::convert_mode_to_full_name($mode_mod->mode) . " on " . APP_NAME;
  }
);
$Router->get(
  "/u/:id/:sub",
  "user/show",
  constraints: [
    "id" => "\d+",
  ],
  title: function ($params) {
    $User = User::find($params["id"]);

    return $User->name . " × " . ucwords($params["mode"]) . " on " . APP_NAME;
  }
);
$Router->get(
  "/u/:id/:sub/:mode",
  "user/show",
  constraints: [
    "id" => "\d+",
  ],
  title: function ($params) {
    $User = User::find($params["id"]);

    return $User->name . " × " . Gamemode::convert_mode_to_full_name($params["mode"]) . " on " . APP_NAME;
  }
);
$Router->get(
  "/u/:id/:sub/:mode/:mod",
  "user/show",
  constraints: [
    "id" => "\d+",
    "mode" => "[a-zA-Z]+",
  ],
  title: function ($params) {
    $User = User::find($params["id"]);

    return $User->name . " × " . Gamemode::convert_mode_to_full_name($params["mode"]) . " on " . APP_NAME;
  }
);

/**
 * @route /user
 */
// TODO: Build middleware.
// TODO: Use route if path to template is same.
$Router->post("/user/wipe", "user/wipe", return: JSON);
$Router->post("/user/update", "user/update", return: JSON);
$Router->post("/user/update/:var", "user/update", return: JSON);
$Router->post("/user/create", "user/create", return: JSON);
$Router->post("/user/delete", "user/delete", return: JSON);
$Router->post("/user/settings/update", "user/settings/update", return: JSON);
$Router->post("/user/settings/privacy/update", "user/settings/privacy/update", return: JSON);
$Router->get("/user/get-content/:file_name", "user/get-content/index", return: JSON);
$Router->get("/user/buy-premium", "user/buy-premium", return: JSON);
$Router->post("/user/pin/create", "user/pin/create", return: JSON);
$Router->post("/user/pin/delete", "user/pin/delete", return: JSON);

/**
 * @route /editor
 */
$Router->get("/editor", "my/editor/index", title: "Profile-Editor | " . APP_NAME);

/**
 * @route /profile
 */
$Router->post("/profile/update", "profile/update", return: JSON);

/**
 * @route /my
 */
$Router->get("/my/:sub", "my/index", title: "Account Manager | " . APP_NAME);
$Router->get("/my/:sub/:action", "my/index", title: "Account Manager | " . APP_NAME);
$Router->get("/my/:sub/:action/:mode", "my/index", title: "Account Manager | " . APP_NAME);
$Router->get("/my/:sub/:action/:mode/:mod", "my/index", title: "Account Manager | " . APP_NAME);

/**
 * @route /relationship
 */
$Router->post("/relationship/create", "relationship/create", return: JSON);
$Router->post("/relationship/delete", "relationship/delete", return: JSON);

/**
 * @route /authentication
 */
$Router->get("/authentication/:type", "authentication/new", return: JSON);
$Router->post("/authentication/create", "authentication/create", return: JSON);
$Router->post("/authentication/update", "authentication/update", return: JSON);
$Router->post("/authentication/delete", "authentication/delete", return: JSON);

/**
 * @route /image
 */
$Router->post("/image/delete", return: JSON);

/**
 * @route /notification
 */
$Router->get("/notification/category/:category", "notification/index", return: JSON);

/**
 * @route /user-manager
 */
$Router->get("/user-manager/:category", "my/index", return: JSON);
$Router->get("/user-manager/:category/:sub", "my/index", return: JSON);
$Router->get("/user-manager/:category/:sub/:var", "my/index", return: JSON);
