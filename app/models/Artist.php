<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Validate\Search;
use Heiakim\Database\Manager as DBM;

class Artist extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "name"
  ];

  /**
   * @var string
   */
  public static $redis_key = "heiakim:artist:";

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,, VIEW ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return string
   */
  public function cover(bool $big_cover = false, ?int $beatmap_set_id = null)
  {
    $beatmap_set_id ??= $this->beatmapsets->first()?->id;
    $big_cover ??= false;
    return include _root() . "/app/templates/helper/beatmaps/_cover.php";
  }

  /**
   * @return ?Beatmap\Set
   */
  public function beatmapsets()
  {
    return $this->belongsToMany(Beatmap\Set::class, 'mapset_artists', 'artist_id', 'mapset_id');
  }

  /**
   * @return ?Feedback
   */
  public function feedback()
  {
    return $this->hasMany(Feedback::class, "reference_id", "id")->where("type", "artist");
  }

  /**
   * @return int
   */
  public function play_count()
  {
    $artist_play_count = 0;
    foreach ($this->beatmapsets as $Set)
      $artist_play_count += $Set->beatmaps->sum("plays");

    return $artist_play_count;
  }

  /**
   * Fetches the most played artists from the last 4000 maps by default.
   *
   * @param string $sort The sort to fetch the artists by.
   * @param int $limit The limit of artists to fetch.
   * @param int $offset The offset of artists to fetch.
   * @return Artist + count.
   */
  public static function find_by_play_rate(
    string $sort = "DESC",
    int $limit = 20,
    int $offset = 0
  ) {
    $scores = (array) (new DBM)->select(
      query: "
      SELECT * FROM high_play_rate_sets
      ORDER BY count $sort
      LIMIT $limit OFFSET $offset
      ",
      fetchAll: true
    );

    // usort($scores, function ($a, $b) {
    //   return $b->count - $a->count;
    // });

    // echo "<pre>";
    // print_r($scores);
    // echo "</pre>";

    $Artists = [];
    $artists = [];

    foreach ($scores as $score) {
      if (count($Artists) == $limit) break;

      $Beatmap = new Beatmap($score->map_md5);

      if (!$Beatmap->data()) continue;

      $artists = $Beatmap->get_featured_artist_names();

      foreach ($artists as $a)
        if (!in_array($a, $artists))
          array_push($artists, $a);

      foreach ($artists as $a) {
        $Artist = new Artist($a);

        if (!$Artist->data()) {
          (new Artist)->create((object) [
            "name" => htmlspecialchars($a),
          ]);
        }

        $push = (object) [
          "Artist" => new Artist(htmlspecialchars($a)),
          "count" => $score->count
        ];
        array_push($Artists, $push);
      }
    }

    return $Artists ? (object) $Artists : null;
  }

  /**
   * Fetches the count of how many times all beatmaps of this
   * artist have been played together already.
   *
   * @return int The play count.
   */
  public function get_play_count()
  {
    $stmt = $this->db->select(
      "
      SELECT SUM(plays) as counter
      FROM maps
      WHERE MATCH (maps.title, maps.artist, maps.version, maps.creator) AGAINST (? IN BOOLEAN MODE)
      ",
      [$this->get_name_weighted_ft_search()]
    )->counter ?? 0;

    return $stmt;
  }

  /**
   * Fetches the count of how many times all beatmaps of this
   * artist have been played by a specific user.
   *
   * @param int $user_id The user id to fetch the play count by.
   * @return int The play count.
   */
  public function get_play_count_by(int $user_id)
  {
    return $this->db->select(
      "
      SELECT count(*) as count
      FROM scores
      JOIN maps ON scores.map_md5 = maps.md5
      WHERE MATCH (maps.title, maps.artist, maps.version, maps.creator) AGAINST (? IN BOOLEAN MODE)
      AND scores.userid = ?
      LIMIT 1
      ",
      [$this->get_name_weighted_ft_search(), $user_id,]
    )->count ?? 0;
  }

  /**
   * Fetches the count of how many beatmaps this artist has.
   *
   * @return int The beatmap count.
   */
  public function get_beatmap_set_count()
  {
    return $this->db->select(
      "
      SELECT COUNT(id) as counter
      FROM maps
      WHERE MATCH (maps.title, maps.artist, maps.version, maps.creator) AGAINST (? IN BOOLEAN MODE)
      LIMIT 1
      ",
      [$this->get_name_weighted_ft_search()],
    )->counter ?? 0;
  }

  /**
   * Fetches the count of how many times this got favorited.
   *
   * @return int The fave count.
   */
  public function get_favorite_count()
  {
    $stmt = $this->db->select(
      "
      SELECT COUNT(id) as counter
      FROM feedback
      WHERE reference_id = ?
      AND type = 'artist'
      ",
      [$this->data()->id]
    )->counter ?? 0;

    return $stmt;
  }

  /**
   * @return string The name of this artist for fulltext search in
   *    format "+*name*"
   */
  public function get_name_weighted_ft_search()
  {
    return Search::ft_params_boolean_mode(
      htmlspecialchars_decode($this->data()->name)
    );
  }
}
