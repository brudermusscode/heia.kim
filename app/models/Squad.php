<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Http\Request;
use Heiakim\Time\Time;
use Heiakim\Application\Logger;
use Heiakim\Utils\Str;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Utils\Utils;
use Heiakim\Model\Image;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Model\Thread\Thread;
use Heiakim\Registry\RedisRegistry;
use Heiakim\Validate\Validate;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

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
   * @return object
   */
  public function edit(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var SquadFeedItem[]
     */
    $Logs = [];

    /**
     * @var Notification[]
     */
    $Notifications = [];

    /**
     * ? Image
     * If an image is sent with the form, return the upload image function.
     */
    if (isset($params->files, $params->image_type))
      return $this->upload_image($params);

    /**
     * ? Name
     */
    if (!empty($params->name) && $params->name !== $this->name) {

      /**
       * @var string
       */
      $params->name = htmlspecialchars_decode($params->name);

      /**
       * Does the squad have changed their name in the past 30 days?
       */
      if (!$this->can_change_name())
        return $this->error("<strong>Your last name change is less than 30 days ago!</strong>");

      /**
       * Name is available?
       */
      if (self::where("name", $params->name)->whereNot("id", $this->id)->first())
        return $this->error("<strong>This name is already in use.</strong> Please choose another one!");

      /**
       * Name length is valid?
       */
      if (strlen($params->name) > self::$length["name"][1])
        return $this->error("The <strong>name</strong> may only be <strong>" . self::$length["name"][1] . " characters</strong> at max. in length.");

      /**
       * Add SquadFeedItem.
       */
      $Logs[] = $this->logs()
        ->make([
          "type" => "__squad__/edit/name",
          "user_id" => $CurrentUser->id,
          "updated_at" => null,
        ]);

      /**
       * Set the new name and update the last name change timestamp.
       */
      $this->name = $params->name;
      $name_updated = true;
    }

    /**
     * ? Tag
     */
    if (!empty($params->tag) && $params->tag !== $this->tag) {

      /**
       * @var string
       */
      $params->tag = htmlspecialchars_decode($params->tag);

      /**
       * Does the squad have changed their name in the past 30 days?
       */
      if (!$this->can_change_name())
        return $this->error("<strong>Your last name change is less than 30 days ago!</strong>");

      /**
       * Name is available?
       */
      if (self::where("tag", $params->tag)->whereNot("id", $this->id)->first())
        return $this->error("<strong>This name is already in use.</strong> Please choose another one!");

      /**
       * Name length is valid?
       */
      if (strlen($params->tag) > self::$length["tag"][1])
        return $this->error("The <strong>tag</strong> may only be <strong>" . self::$length["tag"][1] . " characters</strong> at max. in length.");

      /**
       * Add SquadFeedItem.
       */
      $Logs[] = $this->logs()
        ->make([
          "type" => "__squad__/edit/tag",
          "user_id" => $CurrentUser->id,
          "updated_at" => null,
        ]);

      /**
       * Set the new name and update the last name change timestamp.
       */
      $this->tag = $params->tag;
      $name_updated = true;
    }

    /**
     * Set name updated at to the current timestamp, if name or
     * tag have been modified.
     */
    if (isset($name_updated)) {
      $this->name_updated_at = date("Y-m-d H:i:s", time());

      /**
       * Create global notification.
       */
      if (!empty($params->notification))
        $Notifications[] = Notification::make([
          "type" => "__clan__/edit/name",
          "reference_id" => $this->id,
        ]);
    }

    /**
     * ? Publicity
     */
    if (isset($params->joinable)) {
      if (!in_array($params->joinable, self::$joinable_map))
        return $this->error();

      /**
       * Add SquadFeedItem.
       */
      $Logs[] = $this->logs()
        ->make([
          "type" => "__squad__/edit/publicity",
          "user_id" => $CurrentUser->id,
          "reference_id" => $params->joinable,
          "updated_at" => null,
        ]);

      /**
       * Set the new publicity.
       */
      $this->joinable = $params->joinable;
    }

    /**
     * ? Modes
     */
    if (isset($params->osu) || isset($params->mania) || isset($params->taiko) || isset($params->ctb)) {

      /**
       * @var array
       */
      $modes = $this->modes;

      /**
       * Set the new mode.
       */
      foreach (Gamemode::$modes_text as $mode) {
        $modes[$mode] = !isset($params->$mode)
          ? $modes[$mode]
          : ($params->$mode > 0 ? 1 : 0);
      }

      $this->modes = $modes;
    }

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Save all & commit!
       */
      $this->save();

      foreach ($Logs as $Log)
        $Log->save();

      foreach ($Notifications as $Notification)
        $Notification->save();

      $this->db_commit();

      return $this->success("<strong>Updated!</strong>");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error();
    }
  }

  /**
   * @return object
   */
  public function remove()
  {

    $this->db_transaction();

    try {

      /**
       * ? Threads, Thread Posts, Thread Post Attachments
       */
      $this->threads()
        ->each(function ($Thread) {
          $Thread->posts()
            ->each(function ($Post) {
              $Post->attachments()->delete();
              $Post->delete();
            });

          $Thread->posts()
            ->delete();

          $Thread->delete();
        });


      /**
       * ? Members
       */
      $this->members()
        ->each(function ($Member) {

          $Member->user->update([
            "clan_id" => 0,
          ]);

          $Member->delete();
        });

      /**
       * ? Images
       */
      $this->images()->delete();

      /**
       * ? Requests
       */
      $this->requests()->delete();

      /**
       * ? Feed Items
       */
      $this->feed_items()->delete();

      /**
       * ? Posts
       */
      $this->posts()->delete();

      /**
       * ? Notifications
       */
      Notification::where("type", "LIKE", "%__clan__%")
        ->orWhere("type", "LIKE", "__comment__/squad%")
        ->where("reference_id", $this->id)
        ->delete();

      /**
       * ? Cache
       */
      foreach (Gamemode::$modes as $gumode) {
        $this->redis()->zrem(RedisRegistry::$leaderboard_keys["squads"] . ":$gumode", $this->id);
        $this->redis()->zrem(RedisRegistry::$leaderboard_keys["squads"] . ":$gumode:tscore", $this->id);
        $this->redis()->zrem(RedisRegistry::$leaderboard_keys["squads"] . ":$gumode:rscore", $this->id);
      }

      /**
       * Delete & commit!
       */
      $this->delete();
      $this->db_commit();

      return $this->success("<strong>Your squad has been deleted!</strong> Create a new or join one at any time.");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error();
    }
  }

  /**
   * Update images based on the type.
   *
   * @param object The params.
   * @return object Default return object.
   */
  public function upload_image(object $params)
  {

    /**
     * Catch any upload error in advance.
     */
    if (isset($params->files["error"]) && $params->files["error"] > 0)
      die($this->error(\Heiakim\File\Upload::error($params->files["error"])));

    /**
     * Temporary image is not available?
     */
    if (!isset($params->files["tmp_name"]) || !$params->files["tmp_name"])
      die($this->error("<strong>No image has been added.</strong>"));

    /**
     * Check if file exists.
     */
    if (!file_exists($params->files["tmp_name"]))
      die("<strong>Temporary image file doesn't exist.</strong>");

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var string
     */
    $token = Utils::random_alpha_token(18);

    /**
     * Begin processing.
     */
    $this->db_transaction();
    try {

      /**
       * @var ImageInterface
       */
      $handle = ImageManager::gd()
        ->read($params->files["tmp_name"]);

      /**
       * @var EncodedImageInterface
       */
      $encoded_image = $handle->encode();
      $file_type = $encoded_image->mediaType();

      /**
       * Final file name with extension attached.
       * @var string
       */
      $file_name = "$token.webp";

      // TODO: Premium for squads (all users can buy + stack).

      /**
       * GIF only for Premium+ squads.
       */
      if (str_contains(strtolower($file_type), "gif"))
        die($this->error("GIFs can not be uploaded by now, but will be available soon."));

      /**
       * @var ?string
       */
      $save_path = match ($params->image_type) {
        "logo" => CLAN_LOGO . $file_name,
        "headline" => CLAN_HEADLINE . $file_name,
        default => null,
      };

      /**
       * Image type is invalid?
       */
      if (!$save_path)
        return $this->error("<strong>Invalid type of image has been submitted.</strong>");

      /**
       * Upload the image to the given save path.
       */
      $handle
        ->scale(320)
        ->toWebp()
        ->save($save_path);

      /**
       * Update squad's image.
       */
      $this->update([
        $params->image_type => $file_name,
      ]);

      /**
       * Create image
       */
      $Image = $CurrentUser
        ->images()
        ->create([
          "type" => "__squad__/$params->image_type",
          "reference_id" => $this->id,
          "url" => $file_name,
        ])
        ->fresh();

      /**
       * Create a log.
       */
      $this->feed_items()
        ->create([
          "user_id" => $CurrentUser->id,
          "type" => "__squad__/edit/image+$params->image_type",
          "reference_id" => $Image->id,
          "updated_at" => null,
        ]);

      /**
       * Commit!
       */
      $this->db_commit();

      return $this->success("<strong>" . ucfirst($params->image_type) . " updated!</strong> It can take some time for the image to show up.");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      die($this->error());
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
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,, BANK ,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

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
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,, MEMBERS ,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

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
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,, REQUESTS ,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

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
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,, THREADS ,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return ?Thread
   */
  public function threads()
  {
    return $this->hasMany(Thread::class, "clan_id", "id");
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,, LOGS ,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return SquadFeedItem
   */
  public function logs()
  {
    return $this->hasMany(SquadFeedItem::class, "clan_id", "id")
      ->whereNotIn("type", ["__member__/post"]);
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,, CONTENT ,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

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
