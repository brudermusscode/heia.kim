<?php

namespace Heiakim\Job;

use Heiakim\Job;
use Heiakim\Model\ApiOsu;

class ApiOsuCacheLeaderboard extends Job
{

  /**
   * @return void
   */
  public static function run(string $interval = "+1 day")
  {

    if (static::has_run_before($interval)) return;

    # Caches osu! leaderboards to specified key.
    $Cache = ApiOsu::get()->cache_leaderboard(count: 10);

    # Caching failed?
    if (!$Cache) {
      echo "failed!\n";
      return;
    }

    # Success!
    echo "done!\n";
  }
}
