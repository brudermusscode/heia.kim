<?php

namespace Heiakim\Registry;

abstract class RedisRegistry
{

  /**
   * Keys for leaderboard specific keys.
   */
  public static array $leaderboard_keys = [
    "osu!" => "osu!:leaderboard", // + $gumode + $country + $type (tscore/rscore/pp)
    "squads" => "leaderboard:squads", // + mode + type (tscore/rscore/pp)
    "players" => "leaderboard:development", // + $gumode + $country + $type
  ];

  /**
   * Keys for API specific redis keys.
   */
  public static array $api_keys = [
    "osu!" => [
      "ranking" => "osu!:ranking", # + osu,mania,taiko,fruits
    ]
  ];
}
