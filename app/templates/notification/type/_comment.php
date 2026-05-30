<?php

$type_icon = "interests";

include match ($sub_types[1]) {
  "score" => __DIR__ . "/comment/_score.php",
  default => $unavailable,
};
