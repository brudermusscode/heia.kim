<?php

namespace Heiakim\Registry;

use Heiakim\Model\ConnectionOsu;
use Heiakim\Model\ConnectionDiscord;
use Heiakim\Model\ConnectionGithub;

abstract class ApiConnectionRegistry
{

  /**
   * Mapping for classes that represent a connection between a user
   * that has granted access through a vendor's API to our applica-
   * tion for accessing their information on the vendor's side.
   */
  public static array $map = [
    "osu!" => \Heiakim\Model\ConnectionOsu::class,
    "discord" => \Heiakim\Model\ConnectionDiscord::class,
    "github" => \Heiakim\Model\ConnectionGithub::class,
  ];

  /**
   * @param string $key
   * @return ConnectionOsu|ConnectionDiscord|ConnectionGithub
   *
   * NOTE: Will die when no class was found in mapping.
   */
  public static function ClassOrDie(string $key)
  {
    return !empty(static::$map[$key])
      ? new static::$map[$key]()
      : die(error("Invalid connection provider."));
  }
}
