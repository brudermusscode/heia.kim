<?php

namespace Heiakim\Job;

use Heiakim\Job;
use Heiakim\Model\Leaderboard;

class CacheLeaderboard extends Job
{

  /**
   * @return void
   */
  public static function run(string $interval = "+1 week")
  {

    if (static::has_run_before($interval)) return;

    # Caching failed?
    if (!new Leaderboard()->cache()) {
      echo "failed.\n";
      return;
    }

    # Caching failed?
    if (!new Leaderboard()->cache(climb: true)) {
      echo "failed.\n";
      return;
    }

    # Success!
    echo "done!\n";
  }
}
