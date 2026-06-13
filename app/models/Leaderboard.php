<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Database\Manager as DBM;
use Heiakim\Exception\JobException;
use Heiakim\Model\Country;
use Heiakim\Registry\RedisRegistry;
use Illuminate\Support\Collection;
use RedisException;

class Leaderboard extends Justin
{

  /**
   * Set this class to have no database table associated with it.
   */
  protected $table = null;
  public $timestamps = null;

  public static array $types = [
    "performance",
    "score",
  ];

  public static array $models = [
    "players",
    "squads",
  ];

  public static array $order = [
    "pp",
    "tscore",
    "rscore",
    "acc",
  ];

  /**
   * @param int $mode as gumode
   * @param string $country
   * @param string $sort
   * @param int $offset
   * @return array
   */
  public function view(
    int $mode,
    string $country = "global",
    string $sort = "pp",
    $limit = 50,
    int $offset = 0
  ) {

    /**
     * @var \Redis
     */
    $Redis = $this->redis();

    $redis_key  = RedisRegistry::$leaderboard_keys["players"] . ":$mode";
    $redis_key .= $country && !in_array($country, ["global", "xx"]) ? ':' . $country : '';
    $redis_key .= $sort !== "pp" ? ":$sort" : "";

    $lb = $Redis->zrevrange(
      $redis_key,
      $offset,
      $offset + $limit,
      ["withscores" => true]
    );
    $lb["count"] = $Redis->zcard($redis_key);

    return $lb;
  }

  /**
   * @param int $gumode
   * @return Collection<Country>
   */
  public static function countries(int $gumode)
  {

    return Country::query()
      ->whereHas('users', function ($q) use ($gumode) {
        $q->where('priv', '>', 2)
          ->whereHas('stats', function ($q) use ($gumode) {
            $q->where('mode', $gumode)
              ->where('acc', '>', 0);
          });
      })
      ->orderByDesc('abbreviation')
      ->get();

    return User::selectRaw("COUNT(country) count, country")
      ->whereHas("stats", function ($q) use ($gumode) {
        $q->where("stats.mode", $gumode)
          ->where("stats.acc", ">", 0.00);
      })
      ->join('countries', 'countries.abbreviation', '=', 'users.country')
      ->where("priv", ">", 2)
      ->groupBy("countries.abbreviation")
      ->orderByDesc("countries.abbreviation")
      ->get();
  }

  /**
   * Adds newly discovered country abbreviations to the database.
   *
   * @return void
   */
  public function update_countries()
  {

    /**
     * @var Collection<User>
     */
    $Users = User::select("country")
      ->groupBy("country")
      ->get();

    foreach ($Users as $User)
      if (!Country::where("abbreviation", $User->country)->first())
        Country::create(["abbreviation" => $User->country]);
  }

  /**
   * Caches all leaderboards including country specific ones.
   *
   * @return bool
   */
  public function cache(bool $climb = false)
  {

    try {
      $Redis = $this->redis();
      $base_key = RedisRegistry::$leaderboard_keys[$climb ? "players-climb" : "players"];

      # Iterate through every gumode.
      foreach (Gamemode::$modes as $mode) {

        /**
         * @var Collection<User>
         */
        $Users = User::join('stats', 'users.id', '=', 'stats.id')
          ->select('users.id as user_id', 'tscore', 'rscore', 'pp')
          ->where('mode', $mode)
          ->where('users.priv', '>', 2)
          ->where('stats.pp', '>', 0)
          ->where('stats.acc', '>', 0.000)
          ->orderByDesc('stats.pp')
          ->get();

        foreach ($Users as $User) {

          # Add performance.
          $Redis->zadd("$base_key:$mode", $User->pp, $User->user_id);

          # Add rscore.
          $Redis->zadd("$base_key:$mode:rscore", $User->rscore, $User->user_id);

          # Add tscore.
          $Redis->zadd("$base_key:$mode:tscore", $User->tscore, $User->user_id);
        }

        # ? Country specific.
        foreach (
          $climb ? Country::all() : Leaderboard::countries($mode) as $Country
        ) {

          /**
           * @var Collection<User>
           */
          $Users = User::join('stats', 'users.id', '=', 'stats.id')
            ->select('users.id as user_id', 'users.country', 'tscore', 'rscore', 'pp')
            ->where('mode', '=', $mode)
            ->where('users.priv', '>', 2)
            ->where('users.country', '=', $Country->abbreviation)
            ->where('stats.pp', '>', 0)
            ->where('stats.acc', '>', 0.000)
            ->orderByDesc('stats.pp')
            ->get();

          # Continue if no Users have been found for this country abbreviation.
          if (!$Users) continue;

          foreach ($Users as $User) {

            # Add performance.
            $Redis->zadd("$base_key:$mode:$User->country", $User->pp, $User->user_id);

            # Add rscore.
            $Redis->zadd("$base_key:$mode:$User->country:rscore", $User->rscore, $User->user_id);

            # Add tscore.
            $Redis->zadd("$base_key:$mode:$User->country:tscore", $User->tscore, $User->user_id);
          }
        }
      }

      return true;
    } catch (JobException $e) {
      new $e;
      return false;
    }
  }
}
