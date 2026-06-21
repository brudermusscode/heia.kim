<?php

use Heiakim\Application\Router;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Squad;

/**
 * @var Router $Router
 */
$Router->get(
  "/squad/:id/:page/:mode_o_sub/:mod",
  "squad/show",
  constraints: [
    "id" => "\d+",
  ],
  title: function ($params) {
    $Squad = Squad::find($params["id"]);
    $page = Gamemode::mode_full($params["page"]) ?: $params["page"];

    return ucwords($page) . " 🏆 " . $Squad?->name ?? "Vanished" . " on " . APP_NAME;
  }
);
$Router->get(
  "/squad/:id/:page/:mode_o_sub",
  "squad/show",
  constraints: [
    "id" => "\d+",
  ],
  title: function ($params) {
    $Squad = Squad::find($params["id"]);
    $page = Gamemode::mode_full($params["page"]) ?: $params["page"];

    return ucwords($page) . " 🏆 " . $Squad?->name ?? "Vanished" . " on " . APP_NAME;
  }
);
$Router->get(
  "/squad/:id/:page",
  "squad/show",
  constraints: [
    "id" => "\d+",
  ],
  title: function ($params) {
    $Squad = Squad::find($params["id"]);
    $page = Gamemode::mode_full($params["page"]) ?: $params["page"];

    return ucwords($page) . " 🏆 " . $Squad?->name ?? "Vanished" . " on " . APP_NAME;
  }
);
$Router->get(
  "/squad/:id",
  "squad/show",
  constraints: [
    "id" => "\d+",
  ],
  title: function ($params) {
    $Squad = Squad::find($params["id"]);

    return "🏆 " . $Squad?->name ?? "Vanished" . " on " . APP_NAME;
  }
);
$Router->get("/squad/fetch",  "squad/fetch",  return: JSON);
$Router->get("/squad/get-content/manage/promote-user",  "squad/get-content/manage/promote-user",  return: JSON);
$Router->get("/squad/get-content/manage/restrict-user",  "squad/get-content/manage/restrict-user",  return: JSON);
$Router->get("/squad/get-content/manage/kick-user",  "squad/get-content/manage/kick-user",  return: JSON);
$Router->post("/squad/update",  "squad/update",  return: JSON);
$Router->post("/squad/delete",  "squad/delete",  return: JSON);
$Router->post("/squad/create",  "squad/create",  return: JSON);

$Router->get("/squad/request/new",  "squad/request/new",  return: JSON);
$Router->post("/squad/request/create",  "squad/request/create",  return: JSON);
$Router->post("/squad/request/delete",  "squad/request/delete",  return: JSON);
$Router->post("/squad/request/accept",  "squad/request/accept",  return: JSON);

$Router->post("/squad/user/create",  "squad/user/create",  return: JSON);
$Router->post("/squad/user/update",  "squad/user/update",  return: JSON);
$Router->post("/squad/user/delete",  "squad/user/delete",  return: JSON);
$Router->post("/squad/user/kick",  "squad/user/kick",  return: JSON);

$Router->post("/squad/feed-item/delete",  "squad/feed-item/delete",  return: JSON);

$Router->post("/squad/post/create", return: JSON);
$Router->post("/squad/post/update", return: JSON);
$Router->post("/squad/post/delete", return: JSON);

$Router->post("/squad/post/poll-answer/create", return: JSON);
$Router->post("/squad/post/poll-answer/delete", return: JSON);

$Router->post("/squad/post/vote/create", return: JSON);
$Router->post("/squad/post/vote/delete", return: JSON);

$Router->get("/squads",  "squad/index",  title: "Squads");
$Router->get("/squads/:mode",  "squad/index",  title: "Squads");

$Router->get("/manage/:section",  "manage/index",  title: function ($params) {
  return "Manage " . ucwords($params["section"]) . " | " . APP_NAME;
});
$Router->get("/manage/:section/:sub",  "manage/index",  title: function ($params) {
  return "Manage " . ucwords($params["section"]) . " | " . APP_NAME;
});
$Router->get("/manage/:section/:sub/:action",  "manage/index",  title: function ($params) {
  return "Manage " . ucwords($params["section"]) . " | " . APP_NAME;
});
