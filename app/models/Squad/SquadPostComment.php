<?php

namespace Bruder\Heiakim\Model\Squad;

use Bruder\Justin;
use Bruder\Application\Exception;
use Bruder\Application\Logger;
use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Collection\GroupByUsersCollection;

class SquadPostComment extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "post_id",
    "comment_string",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @param array $models
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function newCollection(array $models = [])
  {
    return new GroupByUsersCollection($models);
  }

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
     * @var SquadPost
     */
    $Post = $params->post;

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
     * Comments disabled?
     */
    if (!$Post->enable_comments)
      return $this->error("<strong>Comments are disabled for this post!</strong>");

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Create it!
       */
      $Comment = $Post
        ->comments()
        ->create([
          "user_id" => $CurrentUser->id,
          "comment_string" => $params->comment_string,
          "updated_at" => null,
        ]);

      $Comment = $Comment->fresh();

      /**
       * Update the post's feedback.
       */
      $Post->update_feedback("comments", +1);

      /**
       * Commit all database transaction changes.
       */
      $this->db_commit();

      /**
       * Append the id to the return object, so the frontend
       * knows, where to attach the comment.
       */
      $this->return->id = $Post->id;

      /**
       * Begin output buffer.
       */
      ob_start();

      /**
       * Variables for the template.
       */
      $CurrentUser;

      /**
       * Set new animation for the frontend.
       */
      $is_new = true;

      /**
       * Include the comment.
       */
      include $this->template("/squad/squad/post/_comment.php");

      /**
       * Put the output into the return object.
       */
      $this->return->data = ob_get_clean();

      return $this->success("<strong>Created!</strong>");
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
     * User is part of the given squad?
     */
    if (!$CurrentUser->squad_user?->can_touch($this))
      return $this->error("<strong>You have no permissions to remove this.</strong>");

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Update the post's feedback.
       */
      $this->post
        ->update_feedback("comments", -1);

      /**
       * Delete it!
       */
      $this->delete();

      /**
       * Commit all database transaction changes.
       */
      $this->db_commit();

      return $this->success("<strong>Deleted!</strong>");
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
   * @return ?Squad
   */
  public function squad()
  {
    return $this->post->squad();
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
}
