<?php

/**
 * @var string
 */
$section = filter_var(get("section"), FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Include the template based on the given section.
 */
include match ($section) {
  "squad" => TEMPLATE . "/squad/manage/_index.php",
  default => UNAVAILABLE,
};
