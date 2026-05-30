<?php

namespace Bruder\Heiakim\Model;

use Bruder\Application\Application;
use Bruder\Heiakim\Model\Gamemode;
use Bruder\Justin;
use Bruder\Heiakim\Enum\BeatmapStatus;
use Bruder\Heiakim\Model\Beatmap\BeatmapRequest;
use Bruder\Heiakim\Model\Beatmap\SetArtist;
use Bruder\Utils\Arr;

class Beatmap extends Justin
{
  /**
   * @var string
   */
  protected $table = "maps";

  /**
   * @var array
   */
  public static $statuses = [
    -1, // NotSubmitted
    0,  // Pending
    1,  // UpdateAvailable
    2,  // Ranked
    3,  // Approved
    4,  // Qualified
    5,  // Loved
  ];

  /**
   * @var array
   */
  public static $status_text = [
    "pending",
    "ranked",
    "loved",
    "approved",
    "qualified"
  ];

  /**
   * @var array
   */
  public static $orders = [
    "id",
    "title",
    "artist",
    "plays",
  ];

  /**
   * @var array
   */
  public static $filter = [
    "tvsize",
  ];

  /**
   * @var string
   */
  public static $redis_key = "heiakim:map:";

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,, REQUESTS ,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return ?BeatmapRequest
   */
  public function requests()
  {
    return $this->hasMany(BeatmapRequest::class, "map_id", "id");
  }

  /**
   * @return bool
   */
  public function ranking_requestable()
  {
    return !in_array($this->status, [BeatmapStatus::RANKED->value, BeatmapStatus::LOVED->value]);
  }

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,, DISPLAY ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return string
   */
  public function cover(bool $big_cover = false, ?int $beatmap_set_id = null)
  {
    $big_cover ??= false;
    $beatmap_set_id ??= $this->set_id;
    return include _root() . "/app/templates/helper/beatmaps/_cover.php";
  }

  /**
   * @return string
   */
  public function stripped_title()
  {
    $patterns = [
      '/\(tv siz[^)]*\)/i',     // Anything starting with (tv siz...
      '/-tv s[^ ]*/i',          // Anything starting with -tv siz...
      '/\[[^\]]*\]/i',          // Anything inside square brackets
      '/\{[^}]*\}/i',           // Anything inside curly braces
      '/\(cut ver[^)]*\)/i',    // Anything starting with (cut ver...
      '/\(short ver[^)]*\)/i',  // Anything starting with (tv siz...
      '/\(featuring[^)]*\)/i',  // Anything starting with (featuring...
      '/\(feat[^)]*\)/i',       // Anything starting with (feat...
      '/\(ft[^)]*\)/i',         // Anything starting with (ft...
      '/\s*featuring.*/i',      // Anything starting with featuring
      '/\s*featured by.*/i',    // Anything starting with featured by
      '/\s*featured.*/i',       // Anything starting with featured
      '/\s*feat\..*/i',         // Anything starting with feat.
      '/\s*ft\..*/i',           // Anything starting with ft.
      '/\(sped up[^)]*\)/i',    // Anything starting with (sped up...
      '/\(sped up[^)]*\)/i',    // Anything starting with (sped up...
    ];

    $cleanedTitle = preg_replace($patterns, '', $this->title);
    $cleanedTitle = trim($cleanedTitle);

    return $cleanedTitle;
  }

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, SET ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return Beatmap\Set
   */
  public function set()
  {
    return $this->belongsTo(Beatmap\Set::class);
  }

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,, SCCORES ,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  /**
   * @return ?Score
   */
  public function scores()
  {
    return $this->hasMany(Score::class, "map_md5", "md5");
  }

  /**
   * @param int $gumode
   * @return ?Score
   */
  public function score_leaderboard(int $gumode, int $limit)
  {
    return $this->scores()
      ->where("mode", $gumode)

      /** Only submitted scores */
      ->where("scores.status", 2)

      /** Only verified users */
      ->with("user")
      ->whereHas("user", function ($q) {
        $q->where("priv", ">", 2);
      })

      /**
       * Only scores that are newer than the last restart of the
       * user of the score.
       */
      ->whereHas("user.settings", function ($subq) {
        $subq->whereRaw("UNIX_TIMESTAMP(account_wiped_at) < UNIX_TIMESTAMP(play_time)")
          ->orWhereNull("account_wiped_at");
      })

      /** Order by pp descending */
      ->selectRaw("*, MAX(pp) as pp, userid")
      ->groupBy("userid")
      ->orderByDesc("pp")
      ->limit($limit)
      ->get();
  }

  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,, ARTISTS ,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/
  /*,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,*/

  public static $base_artist_filter = [
    "[feat.",
    "[feat",
    "(feat.",
    "(feat",
    " feat.",
    " feat ",
    "featuring vocals by",
    " featuring",
    "featured by",
    " featured",
    "(with vocals by",
    "(vocals by",
    " with vocals by",
    " vocals by",
    "(ft.",
    "(ft",
    " ft.",
    " ft",
    " vs.",
    " vs ",
    " x ",
    "/",
  ];

  public function split_artists_in_name(array $filter)
  {
    $name = $this->artist;
    $name  = str_ireplace(self::$base_artist_filter, '~/~', $name);

    if ($filter)
      $name  = str_ireplace($filter, '~/~', $name);

    $name  = preg_replace('/\([^\)]*\)|\[[^\]]*\]$/', '', $name);

    return explode("~/~", $name);
  }

  public function split_artists_in_title(?array $filter = null)
  {
    $title = $this->title;
    $title  = str_ireplace(self::$base_artist_filter, '~/~', $title);

    if ($filter)
      $title  = str_ireplace($filter, '~/~', $title);

    $title  = preg_replace('/\([^\)]*\)|\[[^\]]*\]$/', '', $title);
    $title  = preg_replace('/-[^-]+-/', '', $title);
    return  explode("~/~", $title);
  }

  /**
   * Strips of any bracket and whitespace in front and at the end
   * of a string.
   *
   * Strips off:
   * 1. (), []
   * 2. ~ at beginning and end
   *
   * @param string $str The input string.
   * @return string The string.
   */
  public static function strip_unecessary_chars($str)
  {
    /**
     * Strip brackets and trim
     */
    $str = trim(str_replace(["(", ")", "[", "]"], "", $str));

    /**
     * Remove the last char if it is tilde `~`
     */
    if (isset($str[strlen($str) - 1]) && $str[strlen($str) - 1] == "~")
      $str = substr($str, 0, -1);

    /**
     * Remove first char if it is tilde `~`
     */
    if (isset($str[0]) && $str[0] == "~")
      $str = substr($str, 1);

    /**
     * Iterate through all chars and trim whitespace of. Nothing
     * will be left!
     */
    for ($i = 0; $i < strlen($str); $i++)
      $str = trim($str);

    return $str;
  }

  /**
   * Replaces certain patterns and characters with another to
   * split the artist name string by and check the length of the
   * split array. If it is higher than 1, it is most likely a
   * feature between one or more artists.
   *
   * @return Artist[]
   */
  public function create_featured_artists()
  {
    $filter = [
      "&",
      "&amp;",
      ',',
      " meets ",
      " vs.",
      " vs ",
    ];
    $names = $this->split_artists_in_name($filter);

    /**
     * Look up in title
     */
    $titles = $this->split_artists_in_title();

    /**
     * If no artist is left over (which is very unlikely), return null.
     */
    if (!$names && !$titles)
      return null;

    /**
     * Remove first element of titles name array, since it is the
     * title of the beatmap itself.
     */
    unset($titles[0]);

    /**
     * Trim whitespace from names
     */
    foreach ($names ?? [] as $key => $name)
      $names[$key] = self::strip_unecessary_chars($name);

    /**
     * Trim whitespace from titles
     */
    if (count($titles) > 0)
      foreach ($titles as $title)
        array_push($names, self::strip_unecessary_chars($title));

    $return = [];

    /**
     * Find instances of class Artist for each one and push it to
     * the return array. If no artist is found in the database,
     * create a new one and add it afterwards.
     */
    foreach ($names as $artist) {
      /**
       * Escape the artist name and create a new instance by this
       * name to check whether or not it exists.
       */
      $artist_name = htmlspecialchars($artist);

      /**
       * Continue, if the artist name is empty.
       */
      if (!$artist_name) continue;

      $Artist = Artist::where("name", $artist_name)->first();

      if (!$Artist) {
        $Artist = Artist::create([
          "name" => htmlspecialchars($artist),
        ])->fresh();
      }

      /**
       * Create BeatmapArtist
       */
      if (!$this->set->artists()->find($Artist->id))
        SetArtist::create([
          "mapset_id" => $this->set_id,
          "artist_id" => $Artist->id,
        ])->fresh();

      array_push($return, $Artist);
    }

    return $return;
  }

  /**
   * @return array|null
   */
  public function get_featured_artist_names()
  {
    $filter = [
      "&",
      "&amp;",
      ',',
      " meets ",
      " vs.",
      " vs ",
    ];
    $names = $this->split_artists_in_name($filter);

    if (!$names)
      return null;

    foreach ($names ?? [] as $key => $name)
      $names[$key] = self::strip_unecessary_chars($name);

    return $names;
  }

  /**
   * A job to serialize the artist and title.
   *
   * @return true
   */
  public function insert_artists_from_all()
  {
    $filter = [
      "&",
      "&amp;",
      ',',
      " meets ",
      " vs.",
      " vs ",
    ];
    $names = $this->split_artists_in_name($filter);

    /**
     * Look up in title
     */
    $titles = $this->split_artists_in_title();

    /**
     * If no artist is left over (which is very unlikely), return null.
     */
    if (!$names && !$titles)
      return null;

    /**
     * Remove first element of titles name array, since it is the
     * title of the beatmap itself.
     */
    unset($titles[0]);

    /**
     * Trim whitespace from names
     */
    foreach ($names ?? [] as $key => $name)
      $names[$key] = self::strip_unecessary_chars($name);

    /**
     * Trim whitespace from titles and add each title name to the
     * names array
     */
    if (count($titles) > 0)
      foreach ($titles as $title)
        array_push($names, self::strip_unecessary_chars($title));

    /**
     * Find instances of class Artist for each one and push it to
     * the return array. If no artist is found in the database,
     * create a new one and add it afterwards.
     */
    foreach ($names as $artist) {
      /**
       * Escape the artist name and create a new instance by this
       * name to check whether or not it exists.
       */
      $artist_name = htmlspecialchars($artist);

      /**
       * Continue, if the artist name is empty.
       */
      if (!$artist_name) continue;

      $Artist = new Artist($artist_name);

      if ($Artist->data())
        continue;

      (new Artist)->create((object) [
        "name" => $artist_name,
      ]);
    }

    return true;
  }

  /**
   * @return bool
   */
  public function is_tv_size()
  {
    $is_match = preg_match('/tv size/i', $this->title);

    return $is_match;
  }

  /**
   * @return string
   */
  public function difficulty()
  {
    return $this->turn_difficulty_to_text();
  }

  /**
   * @return string
   */
  public function status()
  {
    return $this->turn_status_to_text();
  }

  /**
   * @return object
   */
  public function stats()
  {
    $return = (object) [];
    $return->diff = [
      $this->diff,
      self::__("Star Difficulty"),
      Beatmap::difficulty_text($this->diff)
    ];
    $return->cs = [
      $this->cs,
      self::__("Circle Size"),
      Beatmap::difficulty_text($this->cs)
    ];
    $return->ar = [
      $this->ar,
      self::__("Approch Rate"),
      Beatmap::difficulty_text($this->ar)
    ];
    $return->od = [
      $this->od,
      self::__("Overall Difficulty"),
      Beatmap::difficulty_text($this->od)
    ];
    $return->hp = [
      $this->hp,
      self::__("HP/Life Drain"),
      Beatmap::difficulty_text($this->hp)
    ];

    return $return;
  }

  /**
   * @return Feedback
   */
  public function feedback()
  {
    return $this->hasMany(Feedback::class, "reference_id", "id")->where("type", "beatmap");
  }

  /**
   * Takes an array as an input containing integers representing
   * gu modes. If any do not match with valid beatmap statusses, they
   * will be removed from the array.
   *
   * @param array $statusses The status array.
   * @param array $fallback The array of statuses to check against
   *    and fallback to.
   * @return array The statusses.
   */
  public static function serialize_statuses(mixed $statuses, mixed $fallback)
  {
    $statuses = Arr::compare_and_remove((array) $statuses, $fallback);
    if (!$statuses) $statuses = $fallback;

    return $statuses;
  }

  /**
   * Return int value of the mode and mod combined as gumode
   */
  public static function gumode(string $mode, string $mod)
  {
    switch ($mode) {
      case 'osu':
        if ($mod === 'vanilla') return 0;
        if ($mod === 'relax') return 4;
        if ($mod === 'autopilot') return 8;
        break;

      case 'taiko':
        if ($mod === 'vanilla') return 1;
        if ($mod === 'relax') return 5;
        break;

      case 'ctb':
        if ($mod === 'vanilla') return 2;
        if ($mod === 'relax') return 6;
        break;

      case 'mania':
        if ($mod === 'vanilla') return 3;
        break;

      default:
        return (bool) false;
    }
  }

  /**
   * Gets the status of a beatmap from int as text
   */
  public static function status_text(int $status)
  {
    return match ($status) {
      -1 => self::__("Not submitted"),
      0 => self::__("Pending"),
      1 => self::__("Update available"),
      2 => 'Ranked',
      3 => self::__("Approved"),
      4 => self::__("Qualified"),
      5 => 'Loved',
      default => 'N/A',
    };
  }

  /**
   * @param string $status
   * @return int
   */
  public static function status_to_int(string $status)
  {
    return match ($status) {
      "not_submitted" => -1,
      "pending" => 0,
      "update_available" => 1,
      "ranked" => 2,
      "approved" => 3,
      "qualified" => 4,
      "loved" => 5,
      default => -1,
    };
  }

  /**
   * Gets the diffculty from float as text.
   *
   * @param mixed $diff The difficulty as a float
   * @return string The diff
   */
  public static function difficulty_text(mixed $diff)
  {
    $diff = round($diff, 2, PHP_ROUND_HALF_EVEN);
    $is_full = (string) number_format($diff, 2);

    if ($is_full == '00') $diff = (int) $diff;

    return match (true) {
      empty($diff) => 'N/A',
      $diff < 2.00 => 'easy',
      $diff < 3.00 => 'normal',
      $diff < 4.00 => 'hard',
      $diff < 5.00 => 'insane',
      $diff < 6.00 => 'extreme',
      $diff < 7.00 => 'wallah',
      $diff < 8.00 => 'holy',
      $diff < 9.00 => 'holymoly',
      $diff < 10.00 => 'dude-stop',
      default => 'hurensohn',
    };
  }

  /**
   * Get mods for a specific beatmap by their bit number
   * Why did you do this peppy? I don't understand nothing
   *
   * @param int $mod The mods as a bitwise number
   * @return string The mod string
   */
  public function turn_difficulty_to_text()
  {
    return self::difficulty_text($this->data()->diff ?? 0);
  }

  /**
   * Get mods for a specific beatmap by their bit number
   * Why did you do this peppy? I don't understand nothing
   *
   * @param int $mod The mods as a bitwise number
   * @return string The mod string
   */
  public function turn_status_to_text()
  {
    return self::status_text($this->status ?? 0);
  }

  /**
   * Return all Gamemodes including gumodes as int array
   */
  public function modes_int()
  {
    return Gamemode::$modes;
  }

  /**
   * Creates the pattern `(?,?,?,?,?)` to use in a mysql query.
   *
   * @param array $arr The array.
   * @return string The query string.
   */
  public static function create_query_string_from_array(array $arr)
  {
    return str_repeat('?,', count($arr) - 1) . '?';
  }
}
