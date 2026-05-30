<?php

namespace Bruder\Heiakim\Job;

use Bruder\Application\Setting;
use Bruder\Http\Request;
use Bruder\Justin;
use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\Leaderboard;
use Bruder\Heiakim\Model\App\AppSettings;
use Bruder\Heiakim\Model\Country;
use Bruder\Heiakim\Model\User;
use Bruder\Time\Time;

class RedisCacheLeaderboardDevelopment extends Justin
{
  /**
   * @var string
   */
  protected $interval = "+1 week";

  /**
   * @return void
   */
  public function execute(?string $interval = null)
  {
    /**
     * @var \Predis\Client
     */
    $Redis = $this->redis();

    /**
     * @var Setting
     */
    $AppSetting = Setting::first();

    /**
     * Week has not yet passed?
     */
    if (!Time::has_passed($AppSetting->ranked_updated_at, $interval ?? $this->interval))
      return;

    /**
     * @var string
     */
    $redis_base_key = Leaderboard::$redis_keys["heiakim"];

    foreach (Gamemode::$modes as $mode) {

      /**
       * @var User
       */
      $Users = User::join('stats', 'users.id', '=', 'stats.id')
        ->select('users.id as user_id', 'tscore', 'rscore', 'pp')
        ->where('mode', $mode)
        ->where('users.priv', '>', 2)
        ->where('stats.pp', '>', 0)
        ->where('stats.acc', '>', 0.000)
        ->orderByDesc('stats.pp')
        ->get();

      if ($Users)
        foreach ($Users as $User) {
          /**
           * Add for pp.
           */
          $Redis->zadd("$redis_base_key:$mode", $User->pp, $User->user_id);

          /**
           * Add for ranked score.
           */
          $Redis->zadd("$redis_base_key:$mode:rscore", $User->rscore, $User->user_id);

          /**
           * Add for t? score.
           */
          $Redis->zadd("$redis_base_key:$mode:tscore", $User->tscore, $User->user_id);
        }

      /**
       * Fetch all countries.
       */
      $Countries = Country::all();

      if ($Countries->count())
        foreach ($Countries as $Country) {

          /**
           * @var User
           */
          $Users = User::join('stats', 'users.id', '=', 'stats.id')
            ->select('users.id as user_id', 'users.country', 'tscore', 'rscore', 'pp')
            ->where('mode', $mode)
            ->where('users.priv', '>', 2)
            ->where('users.country', $Country->abbreviation)
            ->where('stats.pp', '>', 0)
            ->where('stats.acc', '>', 0.000)
            ->orderByDesc('stats.pp')
            ->get();

          /**
           * No users in this country?
           */
          if (!$Users) continue;

          foreach ($Users as $User) {
            /**
             * Add by pp.
             */
            $Redis->zadd("$redis_base_key:$mode:$Country->abbreviation", $User->pp, $User->user_id);

            /**
             * Add by ranked score.
             */
            $Redis->zadd("$redis_base_key:$mode:$Country->abbreviation:rscore", $User->rscore, $User->user_id);

            /**
             * Add by t? score.
             */
            $Redis->zadd("$redis_base_key:$mode:$Country->abbreviation:tscore", $User->tscore, $User->user_id);
          }
        }
    }

    /**
     * Update ranked last updated in app settings.
     */
    $AppSetting->update([
      "ranked_updated_at" => date("Y-m-d H:i:s", time()),
    ]);

    echo "\n" . date("d.m.Y<;>H:i:s") . "<;>Weekly leaderboard rank development updated!<&>\n";
  }
}
