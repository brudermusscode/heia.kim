<?php

$Provider = \Heiakim\Model\ApiOsu::get();

# ---------------------------------------

if (LOGGED)
  include_once __DIR__ . "/_feed.php";
else
  include_once __DIR__ . "/_landing.php";
