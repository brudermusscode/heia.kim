<?php

$type_icon = "campaign";

$file_name = str_replace("+", "_", $sub_types[1]);
$file_path = __DIR__ . "/report/_$file_name.php";

include file_exists($file_path) ? $file_path : $unavailable;
