<?php

namespace Heiakim\Model\Beatmap;

use Heiakim\Application\Application;
use Heiakim\Model\Beatmap;
use Heiakim\Justin;
use Heiakim\Model\Gamemode;
use Heiakim\Validate\Search;
use Heiakim\Model\Comment;
use Heiakim\Model\Artist;

class Set extends Justin
{
  /**
   * @var string
   */
  protected $table = "mapsets";

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,, VIEW ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @param mixed $status
   * @param int $mode
   * @param string $order
   * @param int $limit
   * @return Set
   */
  public static function view(
    ?string $query = null,
    ?string $filter = null,
    mixed $status = [2, 3, 4, 5],
    mixed $mode = [0, 1, 2, 3],
    string $order = "id",
    int $limit = 60,
    int $offset = 0,
  ) {
    $query = filter_var($query ?? "", FILTER_SANITIZE_SPECIAL_CHARS);
    $status = (array) $status;
    $mode = (array) $mode;

    /**
     * Filter
     */
    if ($filter !== null && !in_array($filter, ["tvsize"]))
      $filter = null;

    /**
     * Status
     */
    foreach ($status as $k => $s)
      if (!in_array($s, Beatmap::$status_text) && !in_array($s, Beatmap::$statuses))
        unset($status[$k]);

    if (!$status)
      $status = [2, 3, 4, 5];
    else
      foreach ($status as $key => $s)
        $status[$key] = !is_numeric($s) ? Beatmap::status_to_int($s) : $s;

    /**
     * Mode
     */
    foreach ($mode as $k => $m)
      if (!in_array($m, Gamemode::$modes_text) && !in_array($m, Gamemode::$basic_modes))
        unset($mode[$k]);

    if (!$mode)
      $mode = [0, 1, 2, 3];
    else
      foreach ($mode as $key => $m)
        $mode[$key] = !is_numeric($m) ? Gamemode::mode_int($m) : $m;

    /**
     * Build query
     */
    $BeatmapSets = self::select(["mapsets.*"])
      ->whereHas("beatmaps", function ($q) use ($mode, $status, $query, $filter) {
        $q = $q
          ->whereIn("maps.mode", (array) $mode)
          ->whereIn("maps.status", (array) $status);


        /**
         * Boost search terms for ft search
         */
        if ($query)
          $q = $q->whereRaw("MATCH(title, artist, version, creator) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)]);

        /**
         * Filter
         */
        if ($filter == "tvsize")
          $q = $q->where("title", "LIKE", "%tv size%");
      })
      ->with("beatmaps", function ($q) use ($mode, $status) {
        $q->whereIn("maps.status", (array) $status)
          ->whereIn("maps.mode", (array) $mode);
      });

    /**
     * Order by title
     */
    if ($order == "title")
      $BeatmapSets = $BeatmapSets->selectRaw("first_beatmap.title as first_beatmap_title")
        ->join('maps as first_beatmap', function ($join) {
          $join->on('mapsets.id', '=', 'first_beatmap.set_id')
            ->whereRaw('first_beatmap.id = (SELECT id FROM maps where set_id = mapsets.id ORDER BY title LIMIT 1)');
        })
        ->orderBy('first_beatmap_title');

    /**
     * Order by id, ...
     */
    if (in_array($order, ["id"]))
      $BeatmapSets = $BeatmapSets->orderByDesc("id");

    /**
     * Order by the plays
     */
    if ($order == "plays")
      $BeatmapSets = $BeatmapSets->orderByDesc("max_plays");

    return $BeatmapSets
      ->selectRaw("(SELECT MAX(maps.plays) FROM maps WHERE maps.set_id = mapsets.id) max_plays")
      ->limit($limit)
      ->offset($offset)
      ->get();
  }

  /**
   * @return string
   */
  public function cover(bool $big_cover = false, ?int $beatmap_set_id = null)
  {
    $big_cover ??= false;
    $beatmap_set_id ??= $this->id;
    return include ROOT . "/app/templates/helper/beatmaps/_cover.php";
  }

  /**
   * @var string
   */
  public function stripped_title()
  {
    return $this->beatmaps
      ->first()
      ->stripped_title();
  }

  /**
   * @return int
   */
  public function beatmaps_play_count()
  {
    return $this->beatmaps->sum("plays");
  }

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,, BEATMAPS ,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return Beatmap
   */
  public function beatmaps()
  {
    return $this->hasMany(Beatmap::class);
  }

  /**
   * @return int
   */
  public function play_count()
  {
    return $this->beatmaps()->sum("plays");
  }

  /**
   * @param int $id The id of the beatmap.
   * @return ?Beatmap
   */
  public function contains_beatmap(int $id)
  {
    return $this->beatmaps()
      ->where("id", $id)
      ->first();
  }

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,, ARTISTS ,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return Artist
   */
  public function artists()
  {
    return $this->belongsToMany(Artist::class, "mapset_artists", "mapset_id", "artist_id");
  }

  /**
   * @return void
   */
  public function populate_artists()
  {
    return $this->beatmaps()->first()->create_featured_artists();
  }

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,, COMMENTS ,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return ?Comment
   */
  public function comments()
  {
    return $this->hasMany(Comment::class, "reference_id", "id");
  }
}
