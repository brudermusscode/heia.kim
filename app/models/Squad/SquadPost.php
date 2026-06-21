<?php

namespace Heiakim\Model\Squad;

use Heiakim\Justin;
use Heiakim\Application\Logger;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Squad;
use Heiakim\Model\User;
use Heiakim\Utils\Arr;
use Heiakim\Utils\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
   * @return static
   *
   * NOTE: Will die one error.
   */
  public function new(object $params)
  {

    if (!in_array($params->type, $this->types))
      return die(error());

    # ? Poll
    if ($params->type === "poll") {

      # Make sure atleast 2 options are set.
      if (
        empty($params->options)
        || !is_array($params->options)
        || count($params->options) < 1
        || in_array(false, $params->options)
      )
        return die(error("Give atleast 2 options!"));

      # Validate all options are unique.
      if (count($params->options) !== count(array_unique($params->options)))
        return die(error("All options should be unique!"));

      # No more than 21 options lpelase.
      if (count($params->options) > 21)
        return die(error("Please no more than 30 options!"));

      $options = [];

      # Append each option with a value of zero to an array. The zero will increase
      # as people vote for this option later.
      foreach ($params->options as $option) {
        if (!$option) continue;

        $options[$option] = 0;
      }

      $options["comment_string"] = $params->comment_string["poll"] ?? null;

      if (!$options["comment_string"])
        return die(error("<strong>What is your poll about?</strong>"));

      $params->comment_string = json_encode($options);
    }

    # ? Text
    if ($params->type === "text")
      $params->comment_string = $params->comment_string["text"] ?? null;

    # ? Attachment
    if (isset($params->attachment_type, $params->attachment_id)) {

      /**
       * @var ?Beatmap\Set
       */
      match ($params->attachment_type) {
        SquadPostAttachment::$types[0] => Beatmap\Set::findOrReturn($params->attachment_id, "<strong>This attachment doesn't exist fren!</strong>"),
        default => null,
      };

      /**
       * @var SquadPostAttachment
       */
      $PostAttachment = SquadPostAttachment::make([
        "user_id" => CurrentUser->id,
        "attachment_id" => $params->attachment_id,
        "type" => $params->attachment_type,
      ]);
    }

    if (!$params->comment_string)
      return die(error("Nothing to say? 🤭"));

    $params->enable_comments = isset($params->enable_comments) && $params->enable_comments === 0 ? 0 : 1;

    $this->db_transaction();

    try {

      $Post = CurrentUser->squad
        ->posts()
        ->create([
          "user_id" => CurrentUser->id,
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

      if (isset($PostAttachment) && $PostAttachment instanceof SquadPostAttachment) {
        $PostAttachment->post_id = $Post->id;
        $PostAttachment->save();
      }

      CurrentUser->squad
        ->feed_items()
        ->create([
          "user_id" => CurrentUser->id,
          "type" => "__post__",
          "reference_id" => $Post->id,
          "updated_at" => null,
        ]);

      $this->db_commit();

      return $Post;
    } catch (\Exception $e) {
      Logger::to_file($e);
      $this->db_rollback();

      return die(error());
    }
  }

  /**
   * @param object $params
   * @return static
   *
   * NOTE: Will die on error.
   */
  public function edit(object $params)
  {

    # ? Enable Comments
    $this->enable_comments = isset($params->enable_comments) && $params->enable_comments < 1 ? 0 : 1;

    # ? Comment String
    if (isset($params->comment_string) && Str::length($params->comment_string, 0, 1))
      return die(error("<strong>Nothing to say?</strong> 😗"));

    $this->save();
    $this->db_commit();

    return $this;
  }

  /**
   * @return null
   *
   * NOTE: Will die one error.
   */
  public function remove()
  {

    $this->db_transaction();

    try {

      $this->squad->feed_items()
        ->where([
          "reference_id" => $this->id,
          "type" => "__member__/post"
        ])
        ->get()
        ->each(fn($F) => $F->delete());

      $this->comments()->delete();
      $this->votes()->delete();
      $this->poll_answers()->delete();

      $this->delete();
      $this->db_commit();

      return null;
    } catch (\Throwable $e) {
      Logger::to_file($e);
      $this->db_rollback();

      die(error());
    }
  }

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return BelongsTo<Squad>
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "squad_id", "id");
  }

  /**
   * @return HasOne<SquadFeedItem>
   */
  public function feed_item()
  {
    return $this->hasOne(SquadFeedItem::class, "reference_id", "id")
      ->where("type", "__post__");
  }

  /**
   * @return HasMany<SquadPostVote>
   */
  public function votes()
  {
    return $this->hasMany(SquadPostVote::class, "post_id", "id");
  }

  /**
   * @return HasMany<SquadPostComment>
   */
  public function comments()
  {
    return $this->hasMany(SquadPostComment::class, "post_id", "id");
  }

  /**
   * @return HasMany<SquadPostPollAnswer>
   */
  public function poll_answers()
  {
    return $this->hasMany(SquadPostPollAnswer::class, "post_id", "id");
  }

  /**
   * @return HasOne<SquadPostAttachment>
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
      "feedback" => json_encode($PostFeedback),
    ]);
  }

  /**
   * @return void
   */
  public function upvote(User $by)
  {
    $type = 1;
    $Voted = $by->has_voted_for($this);

    if ($Voted?->type === 1) return;
    if ($Voted) {
      $this->update_feedback($Voted->type === 1 ? "upvotes" : "downvotes", -1);
      $Voted->update(["type" => $type]);

      return;
    }

    $Vote = $this->votes()->make();
    $Vote->type = $type;
    $Vote->user()->associate($by);
    $Vote->save();

    $this->update_feedback("upvotes", 1);
  }

  /**
   * @return void
   */
  public function downvote(User $by)
  {
    $type = -1;
    $Voted = $by->has_voted_for($this);

    if ($Voted?->type === -1) return;
    if ($Voted) {
      $this->update_feedback($Voted->type === 1 ? "upvotes" : "downvotes", -1);
      $Voted->update(["type" => $type]);

      return;
    }

    $Vote = $this->votes()->make();
    $Vote->type = $type;
    $Vote->user()->associate($by);
    $Vote->save();

    $this->update_feedback("downvotes", 1);
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
