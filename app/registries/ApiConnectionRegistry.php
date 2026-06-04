<?php

namespace Heiakim\Registry;

use Heiakim\Model\ConnectionOsu;

abstract class ApiConnectionRegistry
{

  /**
   * Mapping for classes that represent a connection between a user
   * that has granted access through a vendor's API to our applica-
   * tion for accessing their information on the vendor's side.
   */
  public static array $map = [
    "osu!" => \Heiakim\Model\ConnectionOsu::class,
  ];

  /**
   * @param string $key
   * @return class-string<ConnectionOsu>
   *
   * NOTE: Will die when no class was found in mapping.
   */
  public static function ClassOrDie(string $key)
  {
    return static::$map[$key]
      ?? die(error("Invalid connection provider."));
  }
}
