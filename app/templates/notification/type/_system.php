<?php

$type_icon = "browse_activity";

$file_name = str_replace("+", "_", $sub_types[1] ?? "default");
$file_path = __DIR__ . "/system/_$file_name.php";

include file_exists($file_path) ? $file_path : $unavailable;
