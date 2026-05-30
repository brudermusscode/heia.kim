<?php

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

$Router->get("/leaderboard", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:mode", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:mode/:mod", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:mode/:mod/:type", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:mode/:mod/:type/:country", "leaderboard/index", title: "Rankings | " . APP_NAME);
$Router->get("/leaderboard/:mode/:mod/:type/:country/:ppage", "leaderboard/index", title: "Rankings | " . APP_NAME);


    // #maintenance
    // rewrite ^/maintenance /maintenance.php?$query_string last;

    // #legals
    // rewrite ^/legal/(.*)/(.*) /yield.php?page=legal&sub=$1&action=$2&$query_string last;
    // rewrite ^/legal/(.*) /yield.php?page=legal&sub=$1&$query_string last;
    // rewrite ^/legal /yield.php?page=legal&$query_string last;

    // #download
    // rewrite ^/download /yield.php?page=download&$query_string last;

    // #squad > manage
    // rewrite ^/manage/(.*)/(.*)/(.*) /yield.php?page=manage&section=$1&sub=$2&action=$3&$query_string last;
    // rewrite ^/manage/(.*)/(.*) /yield.php?page=manage&section=$1&sub=$2&$query_string last;
    // rewrite ^/manage/(.*) /yield.php?page=manage&section=squad&$query_string last;

    // #editor
    // rewrite ^/editor(.*) /yield.php?page=editor&$query_string last;

    // #scores amk
    // rewrite ^/score/(?<id>[0-9]+) /yield.php?page=score&id=$1$query_string last;

    // #mailings
    // rewrite ^/mailings/(.*) /mailings.php?token=$1$query_string last;

    // #payment options
    // rewrite ^/unlock/premium/(.*) /yield.php?page=unlock&feature=premium&action=$1&$query_string last;
    // rewrite ^/unlock/premium /yield.php?page=unlock&feature=premium&$query_string last;

    // #connect third party applications
    // rewrite ^/connect/(?<vendor>[a-z]+) /yield.php?page=connect&vendor=$1 last;
    // rewrite ^/connect /yield.php?page=connect&vendor= last;

    // rewrite ^/reconnect/(?<vendor>[a-z]+) /yield.php?page=reconnect&vendor=$1 last;
    // rewrite ^/reconnect /yield.php?page=reconnect&vendor= last;