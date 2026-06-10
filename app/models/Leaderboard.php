<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Http\Request;
use Heiakim\Database\Redis;
use Heiakim\Database\Manager as DBM;
use Heiakim\Model\Country;

class Leaderboard extends Justin
{
  protected $table = null;
  public $timestamps = null;

  /**
   * Redis keys to use for caching leaderboards
   *
   * @var array
   */
  public static $redis_keys = [
    "bancho" => "bancho:leaderboard", // + $gumode + $country + $type (tscore, rscore, pp)
    "squads" => "bancho:leaderboard:squads", // + mode + type (tscore, rscore, pp)
    "heiakim" => "heiakim:leaderboard:development", // + $gumode + $country + $type (tscore, rscore, pp)
  ];

  /**
   * @var array
   */
  public static $types = [
    "performance",
    "score",
  ];

  /**
   * @var array
   */
  public static $order = [
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
  public function view(int $mode, string $country = "global", string $sort = "pp", $limit = 50, int $offset = 0)
  {
    /**
     * @var Redis::connect
     */
    $Redis = $this->redis();

    $redis_key  = "bancho:leaderboard:$mode";
    $redis_key .= $country && !in_array($country, ["global", "xx"]) ? ':' . $country : '';
    $redis_key .= $sort !== "pp" ? ":$sort" : "";

    /**
     * @var array
     */
    $lb = $Redis->zrevrange($redis_key, $offset, $offset + $limit, "withscores");
    $lb["count"] = $Redis->zcard($redis_key);

    return $lb;
  }

  /**
   * @param int $mode The gumode 0-7
   * @return mixed Object or null
   */
  public static function get_countries(int $mode)
  {
    return (new DBM)->select(
      "SELECT COUNT(country) count, country
      FROM users
      JOIN stats on stats.id = users.id
      WHERE priv > 2
      AND stats.mode = ?
      and stats.acc > 0.000
      GROUP BY country ORDER BY country DESC",
      [$mode],
      true,
    );
  }

  /**
   * @return object
   */
  public function update_countries()
  {
    /**
     * @var Users
     */
    $Users = User::select("country")->groupBy("country")->get();

    foreach ($Users as $User) {
      if (!Country::where("abbreviation", $User->country)->first())
        Country::create([
          "abbreviation" => $User->country,
        ]);
    }

    return $this->success();
  }
}
