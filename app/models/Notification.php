<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\Beatmap\BeatmapRequest;
use Heiakim\Model\Restriction\Restriction;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostComment;
use Heiakim\Trait\HasDefaultUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Justin
{
  use HasDefaultUser;
  use SoftDeletes;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "type",
    "reference_id",
    "reference_2_id",
    "message",
    "read_at",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  protected static $types = [
    /** System */
    "__system__",
    "__system__/freeze",
    "__system__/unfreeze",
    "__system__/restriction",
    "__system__/restriction+status+updated",
    "__system__/unrestriction",
    "__system__/wipe",

    /** Reports */
    "__report__",
    "__report__/score",
    "__report__/user",
    "__report__/squad",
    "__report__/squad+post",
    "__report__/squad+post+comment",

    /** Clans */
    "__clan__/request/invite",
    "__clan__/request/accepted",
    "__clan__/request/declined",
    "__clan__/edit",
    "__clan__/edit/name",
    "__clan__/edit/owner",
    "__clan__/user/edit+priv+increase",
    "__clan__/user/edit+priv+decrease",
    "__clan__/user/restricted",
    "__clan__/user/setfree",
    "__clan__/user/removed",

    /** Relationships */
    "__relationship__/follow",

    /** Feedback */
    "__feedback__/score",
    "__feedback__/user+birthday",

    /** Comments */
    "__comment__/score",
    "__comment__/squad/score",
  ];

  /**
   * All the types that should only be one element of in the
   * database with the same column values. This could be a like
   * for a score or a new follower.
   *
   * @var array
   */
  protected static $single_types = [
    "__relationship__/follow",
    "__feedback__/score",
    "__feedback__/user+birthday",
  ];

  /**
   * @param object $params
   * @return static
   *
   * NOTE: Will die on error.
   * NOTE: Will not be saved.
   */
  public function new(object|array $params, bool $grouped = true)
  {

    if (is_array($params)) (object) $params;

    $params->reference_id = $params->reference_id ?? null;
    $params->reference_2_id = $params->reference_2_id ?? null;
    $params->message = $params->message ?? null;

    # Validate type.
    if (!$grouped && !in_array($params->type, self::$types))
      return die(error());

    # Validate single type.
    if ($grouped && !in_array($params->type, self::$single_types))
      return die(error());

    # Some notifications should only be shown once, which I call single types. It's
    # like grouped notifications for comments on a score or something. Check if
    # there are more comments of this type for the specific reference and delete all
    # others except the new one.
    if ($grouped && in_array($params->type, self::$single_types)) {
      $Notification = self::where("user_id", $params->user_id)
        ->where("type", $params->type)
        ->where("reference_id", $params->reference_id)
        ->where("reference_2_id", $params->reference_2_id)
        ->first();

      # If a single type notification exists, delete it before a new one is created
      # to prevent spam.
      if ($Notification)
        $Notification->delete();
    }

    $Notification = self::make([
      "user_id" => $params->user_id,
      "type" => $params->type,
      "reference_id" => $params->reference_id,
      "reference_2_id" => $params->reference_2_id,
      "message" => $params->message,
    ]);

    # I do not save here as Notifications most likely will always be sent at the very
    # end of any method, so we can manually save it later.
    // $Notification->save();

    return $Notification;
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ?Score|User|Squad
   */
  public function reference()
  {
    $Reference = match ($this->type) {
      "__system__/freeze",
      "__system__/unfreeze",
      "__system__/restriction+status+updated",
      "__system__/restriction",
      "__system__/unrestriction",
      "__system__/wipe",
      "__system__/premium+gift",
      "__system__/user+priv+increase",
      "__system__/user+priv+decrease",
      "__system__/user+name+change",
      "__system__/user+country+change",
      "__relationship__/follow",
      "__report__/user",
      "__feedback__/user+birthday" => User::class,
      "__report__/score",
      "__feedback__/score",
      "__comment__/score" => Score::class,
      "__clan__/edit/name",
      "__clan__/edit/owner",
      "__report__/squad",
      "__clan__/request/accepted" => Squad::class,
      "__request__/beatmap+created",
      "__request__/beatmap+ranked" => BeatmapRequest::class,
      "__report__/squad+post" => SquadPost::class,
      "__report__/squad+post+comment" => SquadPostComment::class,
      default => null,
    };

    return $this->belongsTo($Reference, "reference_id", "id");
  }

  /**
   * @return ?User|Restriction
   */
  public function reference_2()
  {
    $Reference = match ($this->type) {
      "__feedback__/score",
      "__clan__/request/accepted",
      "__request__/beatmap+created",
      "__request__/beatmap+ranked",
      "__comment__/score" => User::class,
      "__system__/restriction+status+updated",
      "__system__/restriction" => Restriction::class,
      default => null,
    };

    return $this->belongsTo($Reference, "reference_2_id", "id");
  }

  /**
   * @return ?User
   */
  public function squad_member()
  {
    return $this->belongsTo(User::class, "reference_id", "id");
  }

  /**
   * @return ?Squad
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "reference_2_id", "id");
  }

  /**
   * Add a notification system wide to every user.
   *
   * @param object $params The params containing the message.
   * @return bool
   */
  public function create_systemwide(object $params)
  {
    /**
     * Message not set?
     */
    if (!isset($params->message))
      return $this->error("<strong>You have not added a message.</strong>");

    /**
     * Message too short?
     */
    if (strlen(trim($params->message)) < 10)
      return $this->error("<strong>Your message should atleast be 10 character long.</strong>");

    $message = $params->message;

    /**
     * Fetch all user IDs
     */
    $stmt = $this->db->select(
      "SELECT id from users order by id ASC",
      [],
      true
    );

    if (!$stmt) return false;

    $count = 0;
    $table = self::$table;

    $this->db->beginTransaction();

    foreach ($stmt as $user) {
      $count++;
      $current_timestamp = date("Y-m-d H:i:s", time());

      $stmt = $this->db->insert(
        "INSERT INTO $table (user_id, type, reference_id, reference_2_id, message, created_at) values (?, ?, ?, ?, ?, ?)",
        [$user->id, "__system__", null, null, $message, $current_timestamp]
      );

      if (!$stmt) {
        $this->db->rollback();

        return $this->error("<strong>An error occured while sending a notification to user $user->name</strong>. ");
      }
    }

    $this->db->commit();

    return $this->success("<strong>$count notifications</strong> have been sent out.");
  }
}
