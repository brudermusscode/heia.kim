<?php

namespace Heiakim\Application;

use Heiakim\Justin;

class Feature extends Justin
{

  /**
   * @param string $name
   * @return bool
   */
  public static function enabled(string $name)
  {
    return self::where("name", $name)
      ->where("active", 1)
      ->first();
  }
}
