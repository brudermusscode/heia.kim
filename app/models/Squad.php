<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Time\Time;
use Heiakim\Application\Logger;
use Heiakim\Utils\Str;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\File\Upload;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Utils\Utils;
use Heiakim\Model\Image;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Model\Thread\Thread;
use Heiakim\Registry\RedisRegistry;
use Heiakim\Validate\Validate;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Format;
use Intervention\Image\Interfaces\ImageManagerInterface;

class Squad extends Justin
{

  protected $table = "clans";

  protected $fillable = [
    "name",
    "tag",
    "owner",
    "headline",
    "logo",
    "joinable",
    "modes",
    "name_updated_at",
    "performance",
    "deleted_at",
    "updated_at",
  ];

  protected $attributes = [
    "name" => "Vanished",
    "tag" => "NO",
    "owner" => 3,
  ];

  protected $casts = [
    "performance" => "array",
    "modes" => "array",
  ];

  public static array $privileges = [
    "owner" => 2,
    "member" => 1,
  ];

  public static array $joinable_map = [
    "private" => 0, // private, no one can join
    "request" => 1, // requests, people have to request membership
    "public" => 2, // public, everyone can join
  ];

  public static string $name_constraint = '/^[A-Za-z0-9_#&+$!()?\- öäüÖÄÜß]+$/';

  public static string $tag_constraint = '/^[A-Za-z0-9]+$/';

  protected static array $length = [
    "name" => [1, 16],
    "tag" => [1, 6],
  ];

  public static array $default_performance = [
    0 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
    1 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
    2 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
    3 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
    4 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
    5 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
    6 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
    8 => [
      "performance" => 0,
      "accuracy" => 0.00,
      "ranked_score" => 0,
      "total_score" => 0,
      "plays" => 0
    ],
  ];

  /**
   * @return ?Score
   */
  // TODO: Fetch squad intern scores based on join time of members.
  public function view(array $modes, array $status, int $limit, int $offset = 0)
  {

    return Score::with("user")

      /**
       * Only scores that are newer than the last restart of the
       * user of the score.
       */
      ->whereHas("user.settings", function ($subq) {
        $subq->whereRaw("UNIX_TIMESTAMP(account_wiped_at) < UNIX_TIMESTAMP(play_time)")
          ->orWhereNull("account_wiped_at");
      })

      ->with("beatmap")
      ->whereHas("beatmap", function ($q) {
        $q->whereIn("status", [2, 5]);
      })

      ->with("comments")
      ->whereHas('user.squad_user', function ($query) {
        $query->where('clan_id', $this->id);
      })

      ->where("scores.play_time", ">=", $this->created_at)
      ->whereIn("status", $status)
      ->orderBy("play_time", "DESC")
      ->offset($offset)
      ->limit($limit)
      ->get();
  }

  /**
   * @param object $params
   * @return static
   *
   * NOTE: Will die on error.
   */
  public function edit(object $params)
  {

    /**
     * @var SquadFeedItem[]
     */
    $Logs = [];

    /**
     * @var SquadFeedItem[]
     */
    $FeedItems = [];

    /**
     * @var Notification[]
     */
    $Notifications = [];

    $this->db_transaction();

    try {

      # ? Logo/Headline
      if (!empty($params->files["image"]["tmp_name"]) && !empty($params->image_type)) {
        $this->upload_image($params->files["image"], $params->image_type);

        # Create an image for the image gallery.
        $Image = CurrentUser->images()->create([
          "type" => "__squad__/$params->image_type",
          "reference_id" => $this->id,
          "url" => $this->{$params->image_type},
        ]);

        $FeedItems[] = $this->feed_items()->make([
          "user_id" => CurrentUser->id,
          "type" => "__squad__/edit/image+$params->image_type",
          "reference_id" => $Image->id,
        ]);

        $Logs[] = $this->logs()->make([
          "user_id" => CurrentUser->id,
          "reference_id" => $Image->id,
          "type" => "update:$params->image_type",
        ]);
      }

      # ? Name
      if (!empty($params->name)) {
        $this->set_name_invalid($params->name);
        $this->name_updated_at = CURRENT_TIMESTAMP;

        $Logs[] = $this->logs()->make([
          "user_id" => CurrentUser->id,
          "type" => "update:name",
        ]);

        # This will only be sent if the CurrentUser has activated notifying their Mem-
        # bers about changes.
        if (!empty($params->notification))
          $Notifications[] = Notification::make([
            "reference_id" => $this->id,
            "type" => "__clan__/edit/name",
          ]);
      }

      # ? Tag
      if (!empty($params->tag)) {
        $this->set_tag_invalid($params->tag);

        $Logs[] = $this->logs()->make([
          "user_id" => CurrentUser->id,
          "type" => "update:tag",
        ]);
      }

      # ? Joinable
      if (!empty($params->joinable)) {
        if (!in_array($params->joinable, self::$joinable_map))
          return die(error());

        $this->joinable = $params->joinable;

        $Logs[] = $this->logs()->make([
          "user_id" => CurrentUser->id,
          "type" => "update:joinable",
          "reference_id" => $params->joinable,
        ]);
      }

      # ? Modes
      if (
        isset($params->osu)
        || isset($params->mania)
        || isset($params->taiko)
        || isset($params->ctb)
      ) {

        /**
         * @var array
         */
        $modes = $this->modes;

        foreach (Gamemode::$modes_text as $mode) {
          $modes[$mode] = !isset($params->$mode)
            ? $modes[$mode]
            : ($params->$mode > 0 ? 1 : 0);
        }

        $this->modes = $modes;

        $Logs[] = $this->logs()->make([
          "user_id" => CurrentUser->id,
          "type" => "update:modes",
        ]);
      }

      $this->save();

      foreach (array_merge($Logs, $FeedItems, $Notifications) as $Instance)
        $Instance->save();

      $this->db_commit();

      return $this;
    } catch (\Throwable $e) {
      Logger::to_file($e);
      $this->db_rollback();

      return die(error());
    }
  }

  /**
   * @return object
   */
  public function remove()
  {

    /**
     * @var \Redis
     */
    $Redis = $this->redis();

    $this->db_transaction();

    try {

      $this->members()->each(function ($Member) {
        $Member->user->update([
          "clan_id" => 0,
        ]);
        $Member->delete();
      });

      $this->threads()->each(function ($Thread) {
        $Thread->posts()->each(function ($Post) {
          $Post->attachments()->delete();
          $Post->delete();
        });
        $Thread->posts()->delete();
        $Thread->delete();
      });

      $this->images()->delete();
      $this->requests()->delete();
      $this->feed_items()->delete();
      $this->posts()->delete();

      Notification::where("type", "LIKE", "%__clan__%")
        ->orWhere("type", "LIKE", "__comment__/squad%")
        ->where("reference_id", $this->id)
        ->delete();

      # Remove any left overs in the cache.
      foreach (Gamemode::$modes as $gumode) {
        $Redis->zrem(
          RedisRegistry::$leaderboard_keys["squads"] . ":$gumode",
          $this->id
        );
        $Redis->zrem(
          RedisRegistry::$leaderboard_keys["squads"] . ":$gumode:tscore",
          $this->id
        );
        $this->redis()->zrem(
          RedisRegistry::$leaderboard_keys["squads"] . ":$gumode:rscore",
          $this->id
        );
      }

      $this->delete();
      $this->db_commit();
    } catch (\Throwable $e) {
      Logger::to_file($e);
      $this->db_rollback();

      return die(error());
    }
  }

  /**
   * A Squad by now can only be deleted if there are less than 2 members.
   *
   * @return bool
   */
  public function deletable()
  {
    return $this->members->count() < 2;
  }

  /**
   * @param array $files
   * @param string $type
   * @return void
   *
   * NOTE: Will die on error.
   */
  public function upload_image(array $files, string $type)
  {

    # Die immediately on any upload error.
    Upload::error($files);

    try {

      /**
       * @var ImageManagerInterface
       */
      $ImageManager = ImageManager::usingDriver(GdDriver::class);
      $Image = $ImageManager->decodePath($files["tmp_name"]);
      $ClonedImage = clone $Image;

      # Ensure it's WEBP.
      $EncodedImage = $Image->encodeUsingFormat(Format::WEBP, quality: 100);

      # TODO: Premium for squads to upload GIFs.

      $file_type = $EncodedImage->mediaType();

      if (str_contains(strtolower($file_type), "gif"))
        return die(error("GIFs available soon."));

      $file_name = "sq-" . Utils::random_alpha_token(12);
      $save_path = match ($type) {
        "logo" => CLAN_LOGO,
        "headline" => CLAN_HEADLINE,
        default => die("Uhm…?"),
      };

      # Ensure file name is unique amongst all squad images.
      while (file_exists("$save_path/$file_name.webp"))
        $file_name = "sq-" . Utils::random_alpha_token(12);

      # Save Image!
      $EncodedImage->save("$save_path/$file_name.webp");

      # Just set the image here without saving, so we can save or rollback in other
      # methods.
      $this->$type = "$file_name.webp";

      # Scale Image to 180px and save it as a thumb.
      $ClonedImage->scale(height: 180)
        ->encodeUsingFormat(Format::WEBP, quality: 100)
        ->save("$save_path/$file_name-180.webp");

      return $this;
    } catch (\Throwable $e) {
      Logger::to_file($e);
      die(error());
    }
  }

  /**
   * @param string $name
   * @return void
   *
   * NOTE: Will die on error.
   */
  public function set_name_invalid(string $name)
  {

    $name = trim($name);

    # ? In use
    if (self::where("name", $name)->first())
      return die(error("Tag is in use!"));

    $min = self::$length["name"][0];
    $max = self::$length["name"][1];

    # ? Valid length
    if (!Validate::string_length($min, $max, $name))
      return die(error(
        "<strong>Name</strong> should be <strong> $min to $max </strong> chars long.",
      ));

    # ? All chars are valid
    if (!Validate::string_matches(self::$name_constraint, $name))
      return die(error(
        "Only <strong>alphanumeric characters</strong> and <strong>-_#+$&!?()</strong> are allowed in the name!",
      ));

    $this->name = $name;
  }

  /**
   * @param string $tag
   * @return void
   *
   * NOTE: Will die on error.
   */
  public function set_tag_invalid(string $tag)
  {

    $tag = Str::strip_whitespace($tag);

    # ? In use
    if (self::where("tag", $tag)->first())
      return die(error("Tag is in use!"));

    $min = self::$length["name"][0];
    $max = self::$length["name"][1];

    # ? Valid length
    if (!Validate::string_length($min, $max, $tag))
      return die(error(
        "<strong>Tag</strong> should be <strong> $min to $max </strong> chars long.",
      ));

    # ? All chars are valid
    if (!Validate::string_matches(self::$tag_constraint, $tag))
      return die(error(
        "Only <strong>alphanumeric characters</strong> are allowed in the tag!",
      ));

    $this->tag = $tag;
  }

  /**
   * @return void
   */
  public function set_modes()
  {
    $this->modes = [
      "osu" => 1,
      "taiko" => 1,
      "mania" => 1,
      "ctb" => 1,
    ];
    $this->save();
  }

  /**
   * @return void
   */
  public function cache_performance()
  {

    /**
     * @var \Redis
     */
    $Redis = $this->redis();

    foreach (Gamemode::$modes as $gumode) {
      $mode = Gamemode::gumode_to_mode($gumode);

      # Set modes in case any mode is not set. All should always be atleast set.
      if (empty($this->modes[$mode]))
        $this->set_modes();

      # Continue if the mode is disabled fot his Squad.
      if ($this->modes[$mode] == 0)
        continue;

      /**
       * @var array
       */
      $performances = $this->performance;

      $rkey = RedisRegistry::$leaderboard_keys["squads"] . ":$mode";

      $Redis->zadd("$rkey", $performances[$gumode]["performance"], $this->id);
      $Redis->zadd("$rkey:rscore", $performances[$gumode]["ranked_score"], $this->id);
      $Redis->zadd("$rkey:tscore", $performances[$gumode]["total_score"], $this->id);
    }
  }

  /**
   * Calculates the performance for every mode and sets it.
   *
   * @return void
   */
  public function update_performance(?array $skip_members = null)
  {

    $stats = [];
    $members_in_gumodes = [
      0 => 0,
      1 => 0,
      2 => 0,
      3 => 0,
      4 => 0,
      5 => 0,
      6 => 0,
      8 => 0,
    ];
    $count = 0;

    foreach ($this->members as $Member) {

      /**
       * @var SquadUser $Member
       */

      if (!$Member->performance) continue;

      # We don't want certain members specified…
      if ($skip_members !== null && in_array($Member->id, $skip_members))
        continue;

      # …or restricted ones - Excuse me 🙂
      if ($Member->is_restricted() || $Member->user->is_restricted())
        continue;

      $count++;

      foreach (Gamemode::$modes as $gumode) {

        if (empty($Member->performance[$gumode]))
          continue;

        if ($Member->performance[$gumode]["performance"] != 0)
          $members_in_gumodes[$gumode] += 1;

        $stats[$gumode] = [
          "performance" => ($stats[$gumode]["performance"] ?? 0)
            + (int) $Member->performance[$gumode]["performance"],
          "accuracy" => ($Stats[$gumode]["accuracy"] ?? 0)
            + (float) $Member->performance[$gumode]["accuracy"],
          "ranked_score" => ($Stats[$gumode]["ranked_score"] ?? 0)
            + (int) $Member->performance[$gumode]["ranked_score"],
          "total_score" => ($Stats[$gumode]["total_score"] ?? 0)
            + (int) $Member->performance[$gumode]["total_score"],
          "plays" => ($Stats[$gumode]["plays"] ?? 0)
            + (int) $Member->performance[$gumode]["plays"],
        ];
      }
    }

    # Divide the accuracy of each mode by the members count.
    foreach ($stats as $gumode => $values) {
      $stats[$gumode]["accuracy"] = $members_in_gumodes[$gumode] < 1 ? 0.000 : $stats[$gumode]["accuracy"] / $members_in_gumodes[$gumode];
    }

    $this->performance = $stats;
    $this->save();
  }

  /**
   * @return string
   */
  public function link()
  {
    return "/squad/" . $this->id;
  }

  /**
   * @return string
   */
  public function logo()
  {
    $squad_logo = $this->logo;
    return include ROOT . "/app/templates/helper/squads/_image_logo.php";
  }

  /**
   * @return string
   */
  public function headline()
  {
    $squad_headline = $this->headline;
    return include ROOT . "/app/templates/helper/squads/_image_headline.php";
  }

  /**
   * @return ?Image
   */
  public function images()
  {
    return $this->hasMany(Image::class, "reference_id", "id")
      ->where("type", "LIKE", "%__squad__%");
  }

  /**
   * @return string
   */
  public function display_publicity()
  {
    $publicity = array_flip(Squad::$joinable_map)[$this->joinable];

    return $publicity === "request" ? "Closed" : ucfirst($publicity);
  }

  /**
   * @return object
   */
  public function statistics(?int $gumode = null)
  {
    /**
     * @var array
     */
    $stats = [];

    foreach ($this->members as $Member) {
      /**
       * @var array
       */
      $performances = json_decode($Member->performance, true);

      foreach ($performances as $current_gumode => $performance) {

        /**
         * Skip if the mode is disabled.
         */
        if (!$this->gumode_enabled($current_gumode))
          continue;

        $stats[$current_gumode] = [
          "performance"  => ($stats[$current_gumode]["performance"] ?? 0)  + (float) $performance["performance"],
          "accuracy"     => ($Stats[$current_gumode]["accuracy"] ?? 0)     + (float) $performance["accuracy"],
          "ranked_score" => ($Stats[$current_gumode]["ranked_score"] ?? 0) + (int) $performance["ranked_score"],
          "total_score"  => ($Stats[$current_gumode]["total_score"] ?? 0)  + (int) $performance["total_score"],
          "plays"        => ($Stats[$current_gumode]["plays"] ?? 0)        + (int) $performance["plays"],
        ];
      }
    }

    return $gumode !== null && in_array($gumode, Gamemode::$modes)
      ? (object) $stats[$gumode]
      : (object) $stats;
  }

  /**
   * Sums up all performances in specific gumodes for a mode.
   *
   * @return ?object
   */
  public function statistics_all_mods($mode = "osu")
  {
    return (object) $this->performance[$mode];
  }

  /**
   * @return object
   */
  public function placement()
  {
    $placements = [];

    foreach (Gamemode::$modes as $gumode) {
      $placements[$gumode] = (object) [
        "performance" => $this->redis()->zrevrank(RedisRegistry::$leaderboard_keys["squads"] . ":$gumode", $this->id) + 1,
        "ranked_score" => $this->redis()->zrevrank(RedisRegistry::$leaderboard_keys["squads"] . ":$gumode:rscore", $this->id) + 1,
        "total_score" => $this->redis()->zrevrank(RedisRegistry::$leaderboard_keys["squads"] . ":$gumode:tscore", $this->id) + 1,
      ];
    }

    return $placements;
  }

  /**
   * @return bool
   */
  public function gumode_enabled(int $gumode)
  {
    /**
     * @var ?string
     */
    $mode = Gamemode::gumode_to_mode($gumode);

    return $mode && $this->modes[$mode] == 1;
  }

  /**
   * @return SquadUser
   */
  public function chief()
  {
    return $this->members()
      ->whereRaw("(clan_priv & ?) != 0", SquadPrivilege::CHIEF->value)
      ->first();
  }

  /**
   * @return User
   */
  public function users()
  {
    return $this->hasMany(User::class, "clan_id", "id");
  }

  /**
   * @param int $gumode
   * @param int $count
   * @return SquadUser
   */
  public function highest_ranking_members(int $gumode, int $count = 5)
  {

    if (!array_key_exists($gumode, Gamemode::$modes))
      $gumode = 0;

    return $this->members
      ->filter(function ($user) use ($gumode) {
        return ((int) ($user->performance[$gumode]["performance"] ?? 0)) > 0;
      })
      ->sortByDesc(function ($user) use ($gumode) {
        return $user->performance[$gumode]['performance'] ?? 0;
      })->take($count);
  }

  /**
   * @return SquadUser
   */
  public function members()
  {
    return $this->hasMany(SquadUser::class, "clan_id", "id");
  }

  /**
   * @return int
   */
  public function members_count()
  {
    return $this->members->count() - $this->restricted_members_count();
  }

  /**
   * @param SquadPrivilege $Role
   * @return ?SquadUser
   */
  public function role_members(SquadPrivilege $Role)
  {
    return $this->members()
      ->whereRaw("(clan_priv & ?)", $Role->value);
  }

  /**
   * @return ?SquadUser
   */
  public function restricted_members()
  {
    return $this->members()
      ->whereRaw("(clan_priv & ?) = 0", SquadPrivilege::UNRESTRICTED->value)
      ->get();
  }

  /**
   * @return int
   */
  public function restricted_members_count()
  {
    return $this->restricted_members()
      ->count();
  }

  /**
   * @return ?SquadUser
   */
  public function is_member(User $User)
  {
    return $this->members()
      ->where("user_id", $User->id)
      ->first();
  }

  /**
   * @return ?SquadRequest
   */
  public function requests()
  {
    return $this->hasMany(SquadRequest::class, "clan_id", "id");
  }

  /**
   * @return ?SquadRequest
   */
  public function active_requests()
  {
    return $this->requests()
      ->whereNull("done_at")
      ->whereNull("deleted_at");
  }

  /**
   * @return ?SquadRequest
   */
  public function invites()
  {
    return $this->hasMany(SquadRequest::class, "clan_id", "id")
      ->where("type", "invite");
  }

  /**
   * @return ?Thread
   */
  public function threads()
  {
    return $this->hasMany(Thread::class, "clan_id", "id");
  }

  /**
   * @return HasMany<SquadLog>
   */
  public function logs()
  {
    return $this->hasMany(SquadLog::class);
  }

  /**
   * @return ?Squad\SquadPost
   */
  public function posts()
  {
    return $this->hasMany(Squad\SquadPost::class, "squad_id", "id");
  }

  /**
   * @return SquadFeedItem
   */
  public function feed_items()
  {
    return $this->hasMany(SquadFeedItem::class, "clan_id", "id");
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,, PRIVACY ,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * Checks whether or not the current squiad is private
   *
   * @return bool True of false
   */
  public function is_private()
  {
    return $this->joinable === self::$joinable_map["private"];
  }

  /**
   * Checks whether or not the current squiad is private
   *
   * @return bool True of false
   */
  public function is_public()
  {
    return $this->joinable === self::$joinable_map["public"];
  }

  /**
   * Checks whether or not the current squiad is private
   *
   * @return bool True of false
   */
  public function is_requestable()
  {
    return !$this->is_public() && !$this->is_private();
  }

  /**
   * @return ?bool
   */
  public function can_change_name()
  {
    return is_null($this->name_updated_at) || Time::has_passed_since($this->name_updated_at, 30, true);
  }

  /**
   * @return string Timestamp of the time when the squad can
   *    change it's name again.
   */
  public function time_left_for_name_change()
  {
    return !$this->can_change_name()
      ? Time::has_passed_since_return_time($this->name_updated_at, date("Y-m-d H:i:s", time()), 30)
      : 0;
  }
}
