<?php

namespace Bruder\Heiakim\Model\Squad;

use Bruder\Justin;
use Bruder\Application\Exception;
use Bruder\Application\Logger;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\User;
use Bruder\Utils\Arr;
use Bruder\Utils\Str;

class SquadPost extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "squad_id",
    "type",
    "comment_string",
    "feedback",
    "enable_comments",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  public $types = [
    "text",
    "poll",
    "score",
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
     * Type valid?
     */
    if (!in_array($params->type, $this->types))
      return request_error();

    /**
     * ? Poll
     */
    if ($params->type === "poll") {

      /**
       * Enough options set?
       */
      if (!isset($params->options) || !is_array($params->options) || count($params->options) < 1)
        return request_error("<strong>Give some options!</strong>");

      /**
       * Too many options?
       */
      if (count($params->options) > 30)
        return request_error("<strong>Please no more than 30 options!</strong> Senpai!");

      /**
       * @var array
       */
      $options = [];

      /**
       * Append each option with a value of zero to an array. The
       * zero will increase as people vote for this option later.
       */
      foreach ($params->options as $option) {
        if (!$option) continue;

        $options[$option] = 0;
      }

      /**
       * Append the comment string.
       */
      $options["comment_string"] = $params->comment_string["poll"] ?? null;

      /**
       * Comment string is valid?
       */
      if (!$options["comment_string"])
        request_error("<strong>What is your poll about?</strong>");

      /**
       * Set the comment_string to the new array and encode it as json.
       */
      $params->comment_string = Arr::to_json($options);
    }

    /**
     * ? Text
     */
    if ($params->type === "text")
      $params->comment_string = $params->comment_string["text"] ?? null;

    /**
     * ? Attachment
     */
    if (isset($params->attachment_type, $params->attachment_id)) {

      /**
       * @var ?Beatmap\Set|
       */
      match ($params->attachment_type) {
        SquadPostAttachment::$types[0] => Beatmap\Set::findOrReturn($params->attachment_id, "<strong>This attachment doesn't exist fren!</strong>"),
        default => null,
      };

      /**
       * @var PostAttachment
       */
      $PostAttachment = SquadPostAttachment::make([
        "user_id" => $CurrentUser->id,
        "attachment_id" => $params->attachment_id,
        "type" => $params->attachment_type,
      ]);
    }

    /**
     * Comment string set?
     */
    if (!$params->comment_string)
      return request_error("<strong>Nothing to say?</strong> 🤭");

    /**
     * @var int
     */
    $params->enable_comments = isset($params->enable_comments) && $params->enable_comments === 0 ? 0 : 1;

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Create it!
       */
      $Post = $CurrentUser
        ->squad
        ->posts()
        ->create([
          "user_id" => $CurrentUser->id,
          "comment_string" => $params->comment_string,
          "type" => $params->type,
          "feedback" => Arr::to_json([
            "upvotes" => 0,
            "downvotes" => 0,
            "comments" => 0,
          ]),
          "enable_comments" => $params->enable_comments,
          "updated_at" => null,
        ]);

      /**
       * Attach the attachment to the post, if one exists.
       */
      if (isset($PostAttachment) && $PostAttachment instanceof SquadPostAttachment) {
        $PostAttachment->post_id = $Post->id;
        $PostAttachment->save();
      }

      /**
       * Create a SquadFeedItem.
       */
      $CurrentUser
        ->squad
        ->feed_items()
        ->create([
          "user_id" => $CurrentUser->id,
          "type" => "__post__",
          "reference_id" => $Post->id,
          "updated_at" => null,
        ]);

      $this->db_commit();

      $return_msg = "<strong>Posted!</strong> <a href=\"/squad/" . $CurrentUser->squad->id . "#squad-post-$Post->id\">See it here &nbsp; <mi smol>open_in_new</mi>";

      ob_start();

      $is_new = true;
      $CurrentUser;

      include $this->template("/squad/squad/post/_post.php");

      return request_success($return_msg, data: ob_get_clean());
    } catch (\Exception $e) {

      Logger::to_file($e);
      $this->db_rollback();

      return request_error($e->getMessage());
    }
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
     * ? Enable Comments
     */
    $this->enable_comments = isset($params->enable_comments) && $params->enable_comments < 1 ? 0 : 1;

    /**
     * ? Comment String
     */
    if (isset($params->comment_string) && Str::length($params->comment_string, 0, 1))
      return request_error("<strong>Nothing to say?</strong> 😗");

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      $this->save();
      $this->db_commit();

      return request_success("<strong>Updated!</strong>");
    } catch (\Exception $e) {

      Logger::to_file($e);
      $this->db_rollback();

      return request_error();
    }
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove()
  {

    $this->db_transaction();

    try {

      $this->squad
        ->feed_items()
        ->where("reference_id", $this->id)
        ->where("type", "__member__/post")
        ->get()
        ->each(fn($F) => $F->delete());

      $this->comments()
        ->each(fn($C) => $C->delete());
      $this->votes()
        ->each(fn($V) => $V->delete());
      $this->poll_answers()
        ->each(fn($P) => $P->delete());

      $this->delete();
      $this->db_commit();

      return $this->success("<strong>Deleted!</strong>");
    } catch (\Exception $e) {

      Logger::to_file($e);
      $this->db_rollback();

      return $this->error();
    }
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
    return $this->belongsTo(Squad::class, "squad_id", "id");
  }

  /**
   * @return SquadFeedItem
   */
  public function feed_item()
  {
    return $this->hasOne(SquadFeedItem::class, "reference_id", "id")
      ->where("type", "__post__");
  }

  /**
   * @return ?SquadPostVote
   */
  public function votes()
  {
    return $this->hasMany(SquadPostVote::class, "post_id", "id");
  }

  /**
   * @return ?SquadPostComment
   */
  public function comments()
  {
    return $this->hasMany(SquadPostComment::class, "post_id", "id");
  }

  /**
   * @return SquadPostPollAnswer
   */
  public function poll_answers()
  {
    return $this->hasMany(SquadPostPollAnswer::class, "post_id", "id");
  }

  /**
   * @return SquadPostAttachment
   */
  public function attachment()
  {
    return $this->hasOne(SquadPostAttachment::class, "post_id", "id");
  }

  /**
   * @param string $key
   * @param int $count
   * @return bool
   */
  public function update_feedback(string $key, int $count)
  {
    // 1. upvotes
    // 2. downvotes
    // 3. comments

    $PostFeedback = json_decode($this->feedback, true);
    $PostFeedback[$key] = (int) $PostFeedback[$key];
    $PostFeedback[$key] += ($count < 0 ? -abs($count) : abs($count));

    return $this->update([
      "feedback" => Arr::to_json($PostFeedback),
    ]);
  }

  /**
   * @return int Might be negative.
   */
  public function vote_count()
  {
    return (int) $this->feedback()->upvotes - (int) $this->feedback()->downvotes;
  }

  /**
   * @return object
   */
  public function feedback()
  {
    return (object) json_decode($this->feedback, true);
  }
}
