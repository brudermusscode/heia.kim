<?php

use Bruder\Time\Time;

foreach ($Notifications as $Notification) {

  /**
   * @var string
   */
  $timestamp = Time::ago($Notification->created_at, true);

  /**
   * Split up the type in main and sub types. They are
   * seperated by a slash (/). The main type is always the
   * first one and is usually assigned with __ at the
   * beginning and end of the string.
   */
  $sub_types = explode('/', $Notification->type);
  $main_type = $sub_types[0];

  unset($sub_types[0]);

  if ($main_type == "__system__") {
    $count++;
    include dirname(__DIR__) . "/type/_system.php";
  }
}
