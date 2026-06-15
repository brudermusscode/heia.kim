<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Http\Request;
use Heiakim\Time\Time;
use Heiakim\Application\Exception;
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
  /**
   * @var string
   */
  protected $table = "clans";

  /**
   * @var array
   */
  public $fillable = [
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

  /**
   * @var array
   */
  protected $casts = [
    "performance" => "array",
    "modes" => "array",
  ];

  /**
   * @var array
   */
  public static $privileges = [
    "owner" => 2,
    "member" => 1,
  ];

  /**
   * @var array
   */
  public static $joinable = [
    "private" => 0, // private, no one can join
    "request" => 1, // requests, people have to request membership
    "public" => 2, // public, everyone can join
  ];

  /**
   * @var string
   */
  public static $name_constraint = '/^[A-Za-z0-9_#&+$!()?\- öäüÖÄÜß]+$/';

  /**
   * @var string
   */
  public static $tag_constraint = '/^[A-Za-z0-9]+$/';

  /**
   * @var array
   */
  protected static $length = [
    "name" => [1, 16],
    "tag" => [1, 6],
  ];

  /**
   * @var array
   */
  public static $default_performance = [
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
   * @param object $param
   * @return object
   */
  public function new(object $params)
  {
    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * ? Joinable
     * If 'Open for all' was set, set the value to
     * 2 which means open for all.
     * 1 is join by request and
     * 0 is private, which will only be setable through the squad
     * settings in the my page
     */
    $params->joinable = $params->joinable ? 2 : 1;

    /**
     * ? Tag
     * Strip whitespace from tag.
     * @var string
     */
    $params->tag = Str::strip_whitespace($params->tag);

    /**
     * @var Request
     */
    $tag_is_valid = $this->tag_is_valid($params->tag, return_json_string: false);

    /**
     * Tag is valid?
     * ! Error
     */
    if (!$tag_is_valid->status)
      return $tag_is_valid;

    /**
     * ? Name
     * Need to htmlspecialchars_decode() the name, because it
     * has been serialized by the controller validation.
     * @var string
     */
    $params->name = trim(htmlspecialchars_decode($params->name));

    /**
     * @var Request
     */
    $name_is_valid = $this->name_is_valid($params->name, return_json_string: false);

    /**
     * Name is valid?
     * ! Error
     */
    if (!$name_is_valid->status)
      return $name_is_valid;

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Delete pending SquadRequests of current user
       */
      $CurrentUser
        ->squad_requests()
        ->delete();

      /**
       * Validate modes.
       */
      $modes = [
        "osu" => $params->osu ? 1 : 0,
        "taiko" => $params->taiko ? 1 : 0,
        "mania" => $params->mania ? 1 : 0,
        "ctb" => $params->ctb ? 1 : 0,
      ];

      /**
       * @var Squad
       */
      $Squad = self::create([
        "owner" => $CurrentUser->id,
        "name" => $params->name,
        "tag" => $params->tag,
        "joinable" => $params->joinable,
        "modes" => $modes,
        "updated_at" => null,
      ]);

      /**
       * @var Squad
       */
      $Squad = $Squad->fresh();

      /**
       * @var SquadUser
       */
      $SquadUser = $Squad->members()
        ->create([
          "user_id" => $params->CurrentUser->id,
          "clan_priv" => SquadPrivilege::CHIEF->value
            + SquadPrivilege::MEMBER->value
            + SquadPrivilege::UNRESTRICTED->value,
        ]);

      /**
       * Set SquadUsers default performance.
       */
      $SquadUser->update_performance();

      /**
       * Add performance.
       */
      $Squad->update_performance();

      /**
       * Create log.
       */
      $Squad->logs()
        ->create([
          "user_id" => $params->CurrentUser->id,
          "type" => "__squad__/created",
        ]);

      /**
       * Update user.
       */
      $CurrentUser->update([
        "clan_id" => $Squad->id
      ]);

      /**
       * Commit all database transaction changes.
       */
      $this->db_commit();

      /**
       * Cache performance.
       */
      $Squad->cache_performance();
    } catch (\Exception $e) {
      Logger::to_file($e);

      /**
       * Rollback all database transaction changes.
       */
      $this->db_rollback();

      /**
       * ! Error
       */
      return $this->error("!ERROR_TRANSACTION");
    }

    /**
     * * Success
     */
    return $this->success("<strong>Your squad <a extern target='_blank' href='/squad/$Squad->id' sub>($params->tag) $params->name &nbsp; <i class='ri-link-unlink'></i></a> has been created!</strong>");
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
      if (!in_array($params->joinable, self::$joinable))
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
   * @return void
   */
  public function cache_performance()
  {
    /**
     * @var \Predis\Client
     */
    $Redis = $this->redis();

    foreach (Gamemode::$modes as $gumode) {

      /**
       * Skip, if the mode is disabled.
       *
       * @var string
       */
      $mode = Gamemode::gumode_to_mode($gumode);
      if ($this->modes[$mode] == 0)
        continue;

      /**
       * @var array
       */
      $performance = $this->performance;

      /**
       * @var string
       */
      $rkey = RedisRegistry::$leaderboard_keys["squads"] . ":$mode";

      /**
       * Add all keys.
       */
      $Redis->zadd("$rkey", $performance[$gumode]["performance"], $this->id);
      $Redis->zadd("$rkey:rscore", $performance[$gumode]["ranked_score"], $this->id);
      $Redis->zadd("$rkey:tscore", $performance[$gumode]["total_score"], $this->id);
    }

    return;
  }

  /**
   * Calculates the performance for every mode and sets it for the
   * current Squad.
   *
   * @return void
   */
  public function update_performance(?array $skip_members = null)
  {
    /**
     * Keeps track of the overall performance of all players
     * together, exluding the ones that have not played.
     *
     * @var array
     */
    $stats = [];

    /**
     * Keeps track of how many members have played a specific
     * gumode already (pp > 0)
     *
     * @var array
     */
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

    /**
     * Keeps track of how many members are taken into account for
     * performance calculation. Exluding restricted ones.
     *
     * @var int
     */
    $count = 0;

    /**
     * Iterate through all members.
     */
    foreach ($this->members as $Member) {

      /**
       * @var SquadUser $Member
       */

      /**
       * Continue if the member doesn't have any stats yet.
       */
      if (!$Member->performance) continue;


      /**
       * Skip certain members.
       */
      if ($skip_members !== null && in_array($Member->id, $skip_members))
        continue;

      /**
       * Skip on restricted members.
       */
      if ($Member->is_restricted() || $Member->user->is_restricted())
        continue;

      /**
       * @var object
       */
      $member_performance = json_decode($Member->performance, true);

      /**
       * A new member, increase!
       */
      $count++;

      /**
       * Iterate through all gumodes.
       */
      foreach (Gamemode::$modes as $gumode) {

        /**
         * Continue if there are no performances for this gumode yet.
         */
        if (empty($member_performance[$gumode]))
          continue;

        /**
         * The user has played in this gumode, so we increase the
         * members count in the respective array.
         */
        if ($member_performance[$gumode]["performance"] != 0)
          $members_in_gumodes[$gumode] += 1;


        $stats[$gumode] = [
          "performance"  => ($stats[$gumode]["performance"] ?? 0)  + (int) $member_performance[$gumode]["performance"],
          "accuracy"     => ($Stats[$gumode]["accuracy"] ?? 0)     + (float) $member_performance[$gumode]["accuracy"],
          "ranked_score" => ($Stats[$gumode]["ranked_score"] ?? 0) + (int) $member_performance[$gumode]["ranked_score"],
          "total_score"  => ($Stats[$gumode]["total_score"] ?? 0)  + (int) $member_performance[$gumode]["total_score"],
          "plays"        => ($Stats[$gumode]["plays"] ?? 0)        + (int) $member_performance[$gumode]["plays"],
        ];
      }
    }

    /**
     * Divide the accuracy of each mode by the members count.
     */
    foreach ($stats as $gumode => $values) {
      $stats[$gumode]["accuracy"] = $members_in_gumodes[$gumode] < 1 ? 0.000 : $stats[$gumode]["accuracy"] / $members_in_gumodes[$gumode];
    }

    /**
     * Update it!
     */
    $this->performance = $stats;
    $this->save();

    return;
  }

  /**
   * @param string $str
   * @param bool $return_json_string
   * @return Request|string
   */
  public function name_is_valid(string $str, bool $return_json_string = true)
  {
    /**
     * Name is set?
     * ! Error
     */
    if (!$str)
      return $this->error("<strong>Fill in a name!</strong>", $return_json_string);

    /**
     * Name is in use already?
     * ! Error
     */
    if (self::where("name", $str)->first())
      return $this->error(
        "This <strong>name</strong> is already in use. Please choose another one!",
        $return_json_string
      );

    /**
     * Name length is valid?
     * ! Error
     */
    if (!Validate::string_length(self::$length["name"][0], self::$length["name"][1], $str))
      return $this->error(
        "Your <strong>name</strong> may only be <strong>" . self::$length["name"][0] . " to " . self::$length["name"][1] . " characters in length</strong>.",
        $return_json_string
      );

    /**
     * Name chars are valid?
     * ! Error
     */
    if (!Validate::string_matches(self::$name_constraint, $str))
      return $this->error(
        "Only <strong>alphanumeric characters</strong> and <strong>-_#+$&!?()</strong> are allowed in the name!",
        $return_json_string
      );

    /**
     * * Success
     */
    return $this->success(return_json_string: $return_json_string);
  }

  /**
   * @param string $str
   * @param bool $return_json_string
   * @return Request|string
   */
  public function tag_is_valid(string $str, bool $return_json_string = true)
  {

    /**
     * Tag is set?
     * ! Error
     */
    if (!$str)
      return $this->error("<strong>Fill in a tag!</strong>", $return_json_string);

    /**
     * Tag is in use already?
     * ! Error
     */
    if (self::where("tag", $str)->first())
      return (new Request)->error("<strong>This tag is already in use.</strong> Please choose another one!");

    /**
     * Tag length is valid?
     * ! Error
     */
    if (!Validate::string_length(self::$length["tag"][0], self::$length["tag"][1], $str))
      return $this->error(
        "Your <strong>tag</strong> may only be <strong>" . self::$length["tag"][0] . " to " . self::$length["tag"][1] . " characters in length</strong>.",
        $return_json_string
      );

    /**
     * Tag chars are valid?
     * ! Error
     */
    if (!Validate::string_matches(self::$tag_constraint, $str))
      return $this->error(
        "Only <strong>alphanumeric characters</strong> are allowed in the tag!",
        $return_json_string
      );

    /**
     * * Success
     */
    return $this->success(return_json_string: $return_json_string);
  }

  // ? >>>>>>>>>>>>>>>>>> DISPLAY >>>>>>>>>>>>>>>>>>>>

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
    $publicity = array_flip(Squad::$joinable)[$this->joinable];

    return $publicity === "request" ? "Closed" : ucfirst($publicity);
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,, MODES ,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return object
   */
  public function modes()
  {
    return (object) $this->modes;
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
   * @return User
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

    /**
     * Mode is valid?
     */
    if (!array_key_exists($gumode, Gamemode::$modes))
      $gumode = 0;

    return $this->members
      ->filter(function ($user) use ($gumode) {
        $performance = json_decode($user->performance, true);
        return ((int) ($performance[$gumode]["performance"] ?? 0)) > 0;
      })
      ->sortByDesc(function ($user) use ($gumode) {
        $performance = json_decode($user->performance, true);
        return $performance[$gumode]['performance'] ?? 0;
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
    return $this->joinable === self::$joinable["private"];
  }

  /**
   * Checks whether or not the current squiad is private
   *
   * @return bool True of false
   */
  public function is_public()
  {
    return $this->joinable === self::$joinable["public"];
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
