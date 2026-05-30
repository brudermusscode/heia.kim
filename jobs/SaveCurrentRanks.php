<?php

namespace Bruder\Heiakim\Job;

use Bruder\Heiakim\Model\Stat\StatDevelopment;
use Bruder\Job;
use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\Leaderboard;
use Bruder\Heiakim\Model\User;
use DateTime;

class SaveCurrentRanks extends Job
{
  /**
   * @return void
   */
  public function execute()
  {
    /**
     * @var \Predis\Client
     */
    $Redis = $this->redis();

    /**
     * @var StatDevelopment
     */
    $StatDevelopment = StatDevelopment::latest()
      ->first();

    /**
     * When the day is the same than the last saved rank, return.
     */
    if ($StatDevelopment) {
      $currentTimestamp = date("Y-m-d H:i:s", time());
      $databaseTimestamp = $StatDevelopment->created_at;

      $currentDate = new DateTime($currentTimestamp);
      $databaseDate = new DateTime($databaseTimestamp);

      if ($currentDate->format('Y-m-d') === $databaseDate->format('Y-m-d'))
        return;
    }

    foreach (Gamemode::$modes as $mode) {
      /**
       * @var string
       */
      $redis_key  = Leaderboard::$redis_keys["bancho"] . ":$mode";

      /**
       * @var array
       */
      $Leaderboard = $Redis->zrevrange($redis_key, 0, -1);

      foreach ($Leaderboard as $rank => $user_id) {

        /**
         * @var ?User
         */
        $User = User::find($user_id);

        if (!$User) continue;

        /**
         * Get timestamp 30 days ago.
         */
        $thirtyDaysAgo = (new DateTime())
          ->modify('-30 days')
          ->format('Y-m-d H:i:s');

        /**
         * Delete records that are older than 30 days.
         */
        $User->stat_development()
          ->where('created_at', '<', $thirtyDaysAgo)
          ->delete();

        /**
         * Create a new stat development.
         */
        $User->stat_development()
          ->create([
            "mode" => $mode,
            "rank" => $rank + 1,
            "updated_at" => null,
          ]);
      }
    }

    echo "\n" . date("d.m.Y<;>H:i:s") . "<;>Rankings saved!<&>\n";
  }
}
