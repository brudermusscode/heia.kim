<?php

namespace Bruder\Application;

use Bruder\Justin;

class Feature extends Justin
{
  /**
   * @param string $name
   * @return bool
   */
  public static function is_enabled(string $name)
  {
    return self::where("name", $name)
      ->where("active", 1)
      ->first();
  }
}
