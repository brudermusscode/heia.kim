<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\User;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Trait\DeletableBy;
use Heiakim\Trait\HasDefaultUser;

class Comment extends Justin
{
  use HasDefaultUser;
  use DeletableBy;

  /**
   * @var string
   */
  protected $table = "new_comments";

  /**
   * @var array
   */
  protected $fillable = [
    "type",
    "reference_id",
    "reference_2_id",
    "comment_string",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  private static $types = [
    "beatmap",
    "score",
    "squad+score",
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
     * @var string
     */
    $type = $params->type;

    /**
     * @var null
     */
    $reference_2 = null;

    /**
     * Type is valid?
     */
    if (!in_array($params->type, self::$types))
      return $this->error();

    /**
     * @var ?Set|Score
     */
    $Reference = self::validate_reference_with_type($type, $params->id);
    if (!$Reference)
      return $this->error();

    /**
     * ? SCORE
     * These are the comments that will also be shown in the
     * score's overview page and user's profiles.
     */
    if ($type === "score") {
      /**
       * Reference is of type Score?
       */
      if (!($Reference instanceof Score))
        return $this->error();
    }

    /**
     * ? BEATMAP
     */
    if ($type === "beatmap") {
      /**
       * Reference is of type Score?
       */
      if (!($Reference instanceof Set))
        return $this->error();
    }

    /**
     * ? SQUAD+SCORE
     * These are comments which will only be shown in the squad's feed.
     */
    if ($type === "squad+score") {
      /**
       * Reference is of type Score?
       */
      if (!($Reference instanceof Score))
        return $this->error();

      /**
       * Current user and score user have squads?
       */
      if (!$CurrentUser->has_squad() || !$Reference->user->has_squad())
        return $this->error();

      /**
       * Current user is member of same squad as the user?
       */
      if ($CurrentUser->squad->id !== $Reference->user->squad->id)
        return $this->error("!NO_PERMISSIONS");

      /**
       * Set the second reference id for the squad.
       */
      $reference_2 = $CurrentUser->squad->id;
    }

    /**
     * Sanitize the comment.
     */
    $comment_string = filter_var($params->comment_string, FILTER_SANITIZE_SPECIAL_CHARS);

    /**
     * Check length of comment.
     */
    if (strlen(trim($comment_string)) < 1)
      return $this->error("<strong>How deep! Expressing yourself by saying nothing.</strong>");

    /**
     * Create comment.
     */
    $Comment = $params->CurrentUser
      ->comments()
      ->create([
        "type" => $params->type,
        "reference_id" => $params->id,
        "reference_2_id" => $reference_2,
        "comment_string" => $comment_string,
        "updated_at" => null
      ]);

    /**
     * Get fresh comment data.
     */
    $Comment = $Comment->fresh();

    /**
     * Send a notification if the current user didn't comment
     * their own thing.
     */
    if ($Reference?->user->id !== $CurrentUser->id)
      $Reference?->user
        ->notifications()
        ->create([
          "type" => "__comment__/$params->type",
          "reference_id" => $Reference->id,
          "reference_2_id" => $CurrentUser->id,
        ]);

    /**
     * Append the comment HTML element to the return object.
     */
    ob_start();

    $is_new = true;

    $CurrentUser;

    include _root() . "/app/templates/comments/_comment.php";

    $this->return->id = $params->id;
    $this->return->data = ob_get_clean();

    return $this->success("<strong>Comment added!</strong>");
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * Right delete it, if the user is the owner.
     */
    if ($this->user_id === $CurrentUser->id) {
      $this->delete();

      return $this->success("<strong>Deleted!</strong>");
    }

    /**
     * ? SQUAD+SCORE
     */
    if (
      $this->type === "squad+score"
      &&
      (
        !$CurrentUser->squad_user
        || !$CurrentUser->squad_user->can_touch($this, die_on_error: false)
      )
    )
      return $this->error("<strong>You have no permissions to touch this content.</strong>");

    /**
     * Remove it!
     */
    $this->delete();

    return $this->success("<strong>Deleted!</strong>");
  }

  /**
   * @return ?Squad
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "reference_2_id", "id");
  }

  /**
   * @return ?Beatmap|Score
   */
  public function reference()
  {
    $Reference = match ($this->type) {
      "beatmap" => Beatmap\Set::class,
      "score" => Score::class,
      "squad+score" => Score::class,
      default => null,
    };

    return $this->belongsTo($Reference, "reference_id", "id");
  }

  /**
   * @return ?Beatmap|Score
   */
  public function reference_2()
  {
    $Reference = match ($this->type) {
      "squad+score" => Squad::class,
      default => null,
    };

    return $this->belongsTo($Reference, "reference_2_id", "id");
  }

  /**
   * @return ?Beatmap|Score
   */
  public static function validate_reference_with_type(string $type, int $id)
  {
    return match ($type) {
      "beatmap" => Set::find($id),
      "score",
      "squad+score" => Score::find($id),
      default => null
    };
  }
}
