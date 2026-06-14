<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostComment;
use Heiakim\Trait\HasDefaultUser;
use RuntimeException;

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

  public static array $type_map = [
    "score" => Score::class,
    "comment" => Comment::class,
    "user" => User::class,
    "squad:post" => SquadPost::class,
    "squad:post:comment" => SquadPostComment::class,
  ];

  public static array $notification_type_map = [
    "score" => "__report__/score",
    "comment" => "__report__/comment",
    "user" => "__report__/user",
    "squad:post" => "__report__/squad+post",
    "squad:post:comment" => "__report__/squad+post+comment",
  ];

  public static function map(string $key)
  {
    return !empty(static::$type_map[$key])
      ? static::$type_map[$key]
      : throw new RuntimeException("Invalid map key $key in class");
  }

  /**
   * @return ?string
   */
  public function notification_type()
  {
    return static::$notification_type_map[$this->report_type] ?? null;
  }

  /**
   * @return Score|Comment|User|SquadPost|SquadPostComment|null
   */
  public function reference()
  {
    return $this->belongsTo(static::map($this->report_type), "reference_id", "id");
  }

  /**
   * @param string $type
   * @param int $id
   * @return Score|Comment|User|SquadPost|SquadPostComment|null
   *
   * NOTE: Will die on error.
   */
  public static function find_reference_or_die(string $type, int $id)
  {
    return !empty(static::map($type)) ? static::map($type)::find($id) : die();
  }
}
