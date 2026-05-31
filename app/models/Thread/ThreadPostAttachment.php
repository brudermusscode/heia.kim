<?php

namespace Heiakim\Model\Thread;

use Heiakim\Justin;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Model\Score;
use Heiakim\Http\Request;
use Heiakim\Model\Thread\ThreadPost;

class ThreadPostAttachment extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "thread_post_id",
    "reference_id",
    "type",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $types = [
    "score",
    "beatmap",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {
    /**
     * @var ThreadPost
     */
    $Post = $params->thread_post;

    /**
     * Type is valid?
     */
    if (!in_array($params->type, self::$types))
      return $this->error();

    /**
     * Reference id is numeric?
     */
    if (!is_numeric($params->reference_id))
      return $this->error();

    /**
     * Reference is valid?
     */
    $Reference = self::validate_reference_by_type($params->type, $params->reference_id);
    if (!$Reference)
      return $this->error();

    /**
     * ? Score
     */
    if ($Reference instanceof Score) {
      /**
       * User of the post belongs to a squad?
       */
      if (!$Reference->user->squad)
        return $this->error();

      /**
       * Score belongs to the user being in the squad of the thread?
       */
      if ($Reference->user->squad->id !== $Post->thread->user->squad->id)
        return $this->error();
    }

    // ? Beatmap

    /**
     * Create it!
     */
    $Post->attachments()->create([
      "type" => $params->type,
      "reference_id" => $params->reference_id,
      "updated_at" => null,
    ]);

    return Request::modoru($this->return);
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params) {}

  /**
   * @param object $params
   * @return object
   */
  public function edit() {}

  /**
   * @return ThreadPost
   */
  public function post()
  {
    return $this->belongsTo(ThreadPost::class);
  }

  /**
   * @return Score|Set
   */
  public function reference()
  {
    return match ($this->type) {
      "score" => $this->hasOne(Score::class, "id", "reference_id"),
      "beatmap" => $this->hasOne(Set::class, "id", "reference_id"),
      default => null,
    };
  }

  /**
   * @param string $type
   * @param int $id
   * @return ?Score|Set
   */
  public static function validate_reference_by_type(string $type, int $id)
  {
    return match ($type) {
      "score" => Score::find($id),
      "beatmap" => Set::find($id),
      default => null,
    };
  }
}
