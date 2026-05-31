<?php

# Initialize composer autoloading.
require_once dirname(__DIR__) . "/vendor/autoload.php";

# Set custom error handling.
require_once __DIR__ . "/init/error_handling.php";

# Establish a new database connection.
new Heiakim\Database\Database;

# Begin a new PHP session.
new Heiakim\Application\Session;

# Include all init files.
foreach (glob(__DIR__ . "/init/*.php") as $filename) {

  # Extract the filename from glob()'s full filepath.
  $real_filename = str_replace(".php", "", last(explode("/", $filename)));

  # Continue on certain init files.
  if (in_array($real_filename, ["error_handling", "router", "session"]))
    continue;

  include_once $filename;
}

# Init files & definitions, that need to be available after all others.
require_once __DIR__ . "/define.php";
require_once __DIR__ . "/init/session.php";
require_once __DIR__ . "/init/router.php";

# The App cann access this definition if it's initialized!
define("APP_INIT", true);
