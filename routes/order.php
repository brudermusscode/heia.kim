<?php

use Heiakim\Application\Router;

/**
 * @var Router $Router
 */

# ? Unlock & Orders
$Router->get("/unlock/premium", "unlock/premium", title: "Unlock Premium+");
$Router->get("/order/success", "order/success", title: "Success");
$Router->get("/order/cancel", "order/cancel", title: "Canceled");
$Router->get("/order/capture", "order/capture", return: "JSON");
$Router->post("/order/create", "order/create", return: "JSON");
$Router->post("/order/update", "order/update", return: "JSON");
$Router->post("/order/delete", "order/delete", return: "JSON");
