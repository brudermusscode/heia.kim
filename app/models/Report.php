<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;
use Bruder\Heiakim\Model\Squad\SquadPost;
use Bruder\Heiakim\Model\Squad\SquadPostComment;
use Bruder\Heiakim\Trait\HasDefaultUser;

class Report extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "reference_id",
    "user_notification",
    "report_type",
    "comment_string",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $types = [
    "score",
    "comment",
    "user",
    "squad:post",
    "squad:post:comment",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * Type is valid?
     */
    if (!in_array($params->report_type, self::$types))
      return $this->error();

    /**
     * Socially excluded?
     */
    if ($CurrentUser->is_socially_excluded())
      return $this->error("!SOCIALLY_EXCLUDED");

    /**
     * Serialize the comment string.
     */
    $params->comment_string = htmlspecialchars($params->comment_string);

    /**
     * Reference object exists?
     */
    $Reference = self::find_reference_or_die($params->report_type, $params->reference_id);

    /**
     * Create it!
     */
    $Report = $CurrentUser->reports()->create([
      "user_notification" => $params->user_notification ? 1 : 0,
      "reference_id" => $Reference->id,
      "report_type" => $params->report_type,
      "comment_string" => $params->comment_string,
      "updated_at" => null,
    ]);

    /**
     * Create notification if set.
     */
    if ($params->user_notification)
      $CurrentUser->notifications()->create([
        "type" => $Report->notification_type(),
        "reference_id" => $Reference->id,
        "updated_at" => null,
      ]);

    return $this->success("<strong>Your report has been created!</strong> Thank you for your effort to keep this a fun place.");
  }

  /**
   * @return Score|Comment|User|SquadPost|SquadPostComment|null
   */
  public function reference()
  {
    $Reference = match ($this->report_type) {
      static::$types[0] => Score::class,
      static::$types[1] => Comment::class,
      static::$types[2] => User::class,
      static::$types[3] => SquadPost::class,
      static::$types[4] => SquadPostComment::class,
      default => null,
    };

    return $this->belongsTo($Reference, "reference_id", "id");
  }

  /**
   * @param string $type
   * @param int $id
   * @return Score|Comment|User|SquadPost|SquadPostComment|null
   */
  public static function find_reference_or_die(string $type, int $id)
  {
    return match ($type) {
      static::$types[0] => Score::find($id),
      static::$types[1] => Comment::find($id),
      static::$types[2] => User::find($id),
      static::$types[3] => SquadPost::find($id),
      static::$types[4] => SquadPostComment::find($id),
      default => die("<strong>Nothing found!</strong> 🥲y"),
    };
  }

  /**
   * @return string
   */
  public function notification_type()
  {
    return match ($this->report_type) {
      static::$types[0] => "__report__/score",
      static::$types[1] => "__report__/user",
      static::$types[2] => "__report__/comment",
      static::$types[3] => "__report__/squad+post",
      static::$types[4] => "__report__/squad+post+comment",
      default => "",
    };
  }
}
