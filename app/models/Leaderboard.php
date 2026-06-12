<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Database\Manager as DBM;
use Heiakim\Model\Country;
use Heiakim\Registry\RedisRegistry;
use Illuminate\Support\Collection;

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

    /**
     * @var array
     */
    $lb = $Redis->zrevrange($redis_key, $offset, $offset + $limit, "withscores");
    $lb["count"] = $Redis->zcard($redis_key);

    return $lb;
  }

  /**
   * @param int $gumode
   * @return mixed
   */
  public static function get_countries(int $gumode)
  {

    return (new DBM)->select(
      "SELECT COUNT(country) count, country
      FROM users
      JOIN stats on stats.id = users.id
      WHERE priv > 2
      AND stats.mode = ?
      and stats.acc > 0.000
      GROUP BY country ORDER BY country DESC",
      [$gumode],
      true,
    );
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
}
