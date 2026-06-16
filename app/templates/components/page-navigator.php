<?php

require CONFIG . "/get_requirements.php";

$type = filter_input(INPUT_GET, "type");

$file_path = __DIR__ . "/page-navigator/_$type.php";
$file_exists = file_exists($file_path);

ob_start();

if ($file_exists) {
  extract($_GET);
  include $file_path;
}

die(success(data: ob_get_clean()));
