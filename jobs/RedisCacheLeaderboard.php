<?php

namespace Bruder\Heiakim\Job;

use Bruder\Http\Request;
use Bruder\Justin;
use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\Leaderboard;
use Bruder\Heiakim\Model\User;

class RedisCacheLeaderboard extends Justin
{
  public function execute()
  {
    $Redis = $this->redis();
    $base_key = Leaderboard::$redis_keys["bancho"];

    foreach (Gamemode::$modes as $mode) {
      /**
       * Fetch global leaderboard to cache full
       */
      // $stmt = $this->db->select(
      //   "
      //   SELECT users.id AS user_id, tscore, rscore, pp FROM stats
      //   JOIN users ON users.id = stats.id
      //   WHERE mode = ?
      //   AND users.priv > 2
      //   AND stats.pp > 0
      //   AND stats.acc > 0.000
      //   ORDER BY stats.pp DESC
      //   ",
      //   [$mode],
      //   true
      // );

      /**
       * @var User
       */
      $Users = User::join('stats', 'users.id', '=', 'stats.id')
        ->select('users.id as user_id', 'tscore', 'rscore', 'pp')
        ->where('mode', '=', $mode)
        ->where('users.priv', '>', 2)
        ->where('stats.pp', '>', 0)
        ->where('stats.acc', '>', 0.000)
        ->orderBy('stats.pp', 'desc')
        ->get();

      if ($Users)
        foreach ($Users as $User) {
          /**
           * Add for pp.
           */
          $Redis->zadd("$base_key:$mode", $User->pp, $User->user_id);

          /**
           * Add for ranked score.
           */
          $Redis->zadd("$base_key:$mode:rscore", $User->rscore, $User->user_id);

          /**
           * Add for t? score.
           */
          $Redis->zadd("$base_key:$mode:tscore", $User->tscore, $User->user_id);
        }

      /**
       * Fetch all countries.
       */
      $countries = Leaderboard::get_countries($mode);

      if ($countries)
        foreach ($countries as $country) {
          // $stmt = $this->db->select(
          //   "
          //   SELECT users.id AS user_id, users.country AS country, tscore, rscore, pp FROM stats
          //   JOIN users ON users.id = stats.id
          //   WHERE mode = ?
          //   AND users.priv > 2
          //   AND users.country = ?
          //   AND stats.pp > 0
          //   AND stats.acc > 0.000
          //   ORDER BY stats.pp DESC
          //   ",
          //   [$mode, $country->country],
          //   true
          // );

          /**
           * @var User
           */
          $Users = User::join('stats', 'users.id', '=', 'stats.id')
            ->select('users.id as user_id', 'users.country', 'tscore', 'rscore', 'pp')
            ->where('mode', '=', $mode)
            ->where('users.priv', '>', 2)
            ->where('users.country', '=', $country->country)
            ->where('stats.pp', '>', 0)
            ->where('stats.acc', '>', 0.000)
            ->orderByDesc('stats.pp')
            ->get();

          if (!$Users) continue;

          foreach ($Users as $User) {
            /**
             * Add by pp.
             */
            $Redis->zadd("$base_key:$mode:$User->country", $User->pp, $User->user_id);

            /**
             * Add by ranked score.
             */
            $Redis->zadd("$base_key:$mode:$User->country:rscore", $User->rscore, $User->user_id);

            /**
             * Add by t? score.
             */
            $Redis->zadd("$base_key:$mode:$User->country:tscore", $User->tscore, $User->user_id);
          }
        }
    }

    return $this->success("<strong>Leaderboards have been cached!</strong>");
  }
}
