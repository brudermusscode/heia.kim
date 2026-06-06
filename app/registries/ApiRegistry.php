<?php

namespace Heiakim\Registry;

use Heiakim\Model\ApiDiscord;
use Heiakim\Model\ApiOsu;

abstract class ApiRegistry
{

  /**
   * Mapping for classes that provide basic functionality to interact
   * with a provider's API in a non user-grant specific fashion.
   */
  public static array $map = [
    "osu!" => \Heiakim\Model\ApiOsu::class,
    "discord" => \Heiakim\Model\ApiDiscord::class,
    "google" => \Heiakim\Model\ApiDiscord::class,
  ];

  /**
   * Mapping for redis keys.
   */
  public static array $redis_map = [
    "osu!" => [
      "ranking" => "osu!:ranking", # + osu,mania,taiko,fruits
    ]
  ];

  /**
   * @param string $key
   * @return class-string<ApiOsu|ApiDiscord>
   *
   * NOTE: Will die when no class was found in mapping.
   */
  public static function ClassOrDie(string $key)
  {
    return static::$map[$key]
      ?? die(error("Invalid connection provider."));
  }
}
