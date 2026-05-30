<?php

use Bruder\Time\Time;

foreach ($Notifications as $Notification) {

  /**
   * Split up the type in main and sub types. They are
   * seperated by a slash (/). The main type is always the
   * first one and is usually assigned with __ at the
   * beginning and end of the string.
   */
  $sub_types = explode('/', $Notification->type);
  $main_type = $sub_types[0];

  /**
   * Apply the main type manually if there is no sub type available.
   */
  if (in_array($Notification->type, ["__system__"]))
    $main_type = $Notification->type;

  /**
   * @var string
   */
  $timestamp = Time::ago($Notification->created_at, true);

  /**
   * @var string
   */
  $unavailable = dirname(__DIR__) . "/type/_unavailable.php";

  if (!$main_type)
    include $unavailable;
  else {


    /**
     * Increase the count.
     */
    $count++;

    /**
     * @var string
     */
    $main_type = $main_type === "__clan__" ? "squad" : $main_type;

    /**
     * @var string
     */
    $file_name = str_replace("_", "", $main_type);

    /**
     * @var string
     */
    $file_path = dirname(__DIR__) . "/type/_$file_name.php";

    /**
     * Inlcude the file or fallback to unavailable.
     */
    include file_exists($file_path) ? $file_path : $unavailable;
  }
}
