<?php

namespace Heiakim\Model\Squad;

use Heiakim\Application\Exception;
use Heiakim\Application\Logger;
use Heiakim\Justin;
use Heiakim\Model\Squad;
use Heiakim\Model\User;
use Heiakim\Utils\Arr;

class SquadPostPollAnswer extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "post_id",
    "answer_key",
    "deleted_at",
    "updated_at",
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
     * @var ?SquadPost
     */
    $Post = SquadPost::find($params->id);

    /**
     * Post exists?
     */
    if (!$Post)
      return $this->error("<strong>This post doesn't exist!</strong>");

    /**
     * User can interact with the squad?
     */
    if (!$CurrentUser->sqcan_take_action_in($Post->squad))
      return $this->error("<strong>You can't interact with this squad right now, friend.</strong>");

    /**
     * @var array
     */
    $questions = json_decode($Post->comment_string, true);

    /**
     * @var string
     */
    $comment_string = $questions["comment_string"];

    /**
     * Unset the comment_string, so only the questions of the poll are left.
     */
    unset($questions["comment_string"]);

    /**
     * @var int
     */
    $params->answer_key = (int) $params->answer_key;

    /**
     * Check if the answer key is either higher than -1 or not
     * higher than the count of the questions substracting -1 for
     * index starting at 0.
     */
    if ($params->answer_key < 0 || $params->answer_key > (count($questions) - 1))
      return $this->error("<strong>The answer given is invalid!</strong>");

    /**
     * Delete an old answer if on exists.
     */
    $PollAnswer = $Post
      ->poll_answers()
      ->where("user_id", $CurrentUser->id)
      ->first();

    /**
     * Same answer has been given already?
     */
    if ($PollAnswer && $PollAnswer->answer_key === $params->answer_key)
      return $this->error("<strong>You have given this answer.</strong>");

    /**
     * Remove one count from the old answer count.
     */
    if ($PollAnswer) {
      $iota = 0;
      foreach ($questions as $question => $count) {
        if ($iota === $PollAnswer->answer_key) {
          $questions[$question] = (int) $count - 1;
          break;
        }

        $iota++;
      }
    }

    /**
     * Add one count to the selected answer count.
     */
    $iota = 0;
    foreach ($questions as $question => $count) {
      if ($iota === $params->answer_key) {
        $questions[$question] = (int) $count + 1;
        break;
      }

      $iota++;
    }

    /**
     * Push the comment_string back to the array.
     */
    $questions["comment_string"] = $comment_string;

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      $PollAnswer?->delete();

      $Post
        ->poll_answers()
        ->create([
          "user_id" => $CurrentUser->id,
          "answer_key" => $params->answer_key,
          "updated_at" => null,
        ]);

      $Post->update([
        "comment_string" => Arr::to_json($questions),
      ]);

      $this->db_commit();

      /**
       * Begin output buffer.
       */
      ob_start();

      $CurrentUser;

      include $this->template("/squad/squad/post/_post.php");

      return request_success("<strong>Created!</strong>", data: ob_get_clean());
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
  public function edit(object $params)
  {
    return null;

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {


      /**
       * Commit all database transaction changes.
       */
      $this->db_commit();

      return $this->success("<strong>Updated!</strong>");
    } catch (\Exception $e) {
      Logger::to_file($e);

      /**
       * Rollback all database transaction changes.
       */
      $this->db_rollback();

      return $this->error();
    }
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
     * @var SquadPost
     */
    $Post = $this->post;

    /**
     * @var Squad
     */
    $Squad = $Post->squad;

    /**
     * @var array
     */
    $questions = json_decode($this->post->comment_string, true);

    /**
     * Add one count to the selected answer count.
     */
    $iota = 0;
    foreach ($questions as $question => $count) {
      if ($iota === $this->answer_key) {
        $questions[$question] = (int) $count - 1;
        break;
      }

      $iota++;
    }

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      $this->delete();

      $this->post
        ->update([
          "comment_string" => Arr::to_json($questions),
        ]);

      $this->db_commit();

      ob_start();

      $CurrentUser;

      include $this->template("/squad/squad/post/_post.php");

      return $this->success("<strong>Deleted!</strong>", data: ob_get_clean());
    } catch (\Exception $e) {

      Logger::to_file($e);
      $this->db_rollback();

      return request_error();
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
   * @return ?SquadPost
   */
  public function post()
  {
    return $this->belongsTo(SquadPost::class, "post_id", "id");
  }

  /**
   * @return ?Squad
   */
  public function squad()
  {
    return $this->post->squad;
  }
}
