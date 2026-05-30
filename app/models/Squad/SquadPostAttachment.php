<?php

namespace Bruder\Heiakim\Model\Squad;

use Bruder\Justin;
use Bruder\Application\Exception;
use Bruder\Application\Logger;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\Score;
use Bruder\Heiakim\Model\Squad\SquadFeedItem;
use Bruder\Heiakim\Model\User;

class SquadPostAttachment extends Justin
{

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "post_id",
    "attachment_id",
    "type",
  ];

  /**
   * @var array
   */
  public static $types = [
    "beatmap:set",
    "score",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {

    try {

      return $this->success("<strong>Created!</strong>");
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
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {

    try {

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
  public function remove() {}

  /**
   * @return ?Beatmap\Set|
   */
  public function reference()
  {
    return match ($this->type) {
      static::$types[0] => Beatmap\Set::find($this->attachment_id),
      static::$types[1] => Score::find($this->attachment_id),
      default => null,
    };
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return ?Squad
   */
  public function squad()
  {
    return $this->post->squad;
  }

  /**
   * @return SquadFeedItem
   */
  public function post()
  {
    return $this->hasOne(SquadPost::class, "post_id");
  }
}
