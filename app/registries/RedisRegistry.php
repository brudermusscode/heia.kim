<?php

namespace Heiakim\Registry;

abstract class RedisRegistry
{

  /**
   * Leaderboard specific keys.
   */
  public static array $leaderboard_keys = [
    "osu!" => "osu!:leaderboard", // + gumode + country + scoring-type
    "players" => "leaderboard", // + gumode + country + scoring-type
    "players-climb" => "leaderboard:climb", // + gumode + country + scoring-type
    "squads" => "leaderboard:squads", // + mode + scoring-type
  ];

  /**
   * API specific keys.
   */
  public static array $api_keys = [
    "osu!" => [
      "ranking" => "osu!:ranking", # + osu,mania,taiko,fruits
    ]
  ];
}
