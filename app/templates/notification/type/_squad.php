<?php

$type_icon = "workspaces";

$file_name = str_replace("+", "_", $sub_types[2]);
$file_path = __DIR__ . "/squad/$sub_types[1]/_$file_name.php";

include file_exists($file_path) ? $file_path : $unavailable;
