<?php

namespace Bruder\Heiakim\Model\Squad;

use Bruder\Application\Exception;
use Bruder\Application\Logger;
use Bruder\Justin;
use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\User;
use Bruder\Utils\Arr;

class SquadPostVote extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "post_id",
    "type",
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
     * @var SquadPost
     */
    $Post = $params->SquadPost;

    /**
     * @var int
     */
    $params->type = (int) $params->type < 0 ? -1 : 1;

    /**
     * @var ?SquadPostVote
     */
    $Vote = $Post
      ->votes()
      ->where("user_id", $CurrentUser->id)
      ->first();

    /**
     * Vote with this specific type already exists?
     */
    if ($Vote?->type === $params->type)
      return request_error("<strong>You have already voted, friend!</strong>");

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Update an existing vote...
       */
      if ($Vote) {
        $Vote->update([
          "type" => $params->type,
        ]);

        $Post->update_feedback($params->type < 0 ? "upvotes" : "downvotes", -1);
      }

      /**
       * ...or create a new one.
       */
      else {
        $Post->votes()
          ->create([
            "user_id" => $CurrentUser->id,
            "type" => $params->type,
            "updated_at" => null,
          ]);

        $Post->update_feedback($params->type < 0 ? "downvotes" : "upvotes", 1);
      }

      $this->db_commit();

      return request_success("<strong>Voted!</strong>", data: ["has_votes" => $Vote ? true : false]);
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
  public function remove(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var int
     */
    $type = $this->type;

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      $this->delete();

      $this->post->update_feedback($type < 0 ? "downvotes" : "upvotes", -1);

      $this->db_commit();

      return request_success("<strong>Removed!</strong>");
    } catch (\Exception $e) {

      Logger::to_file($e);
      $this->db_rollback();

      return request_error();
    }
  }

  /**
   * @return ?SquadPost
   */
  public function post()
  {
    return $this->belongsTo(SquadPost::class, "post_id", "id");
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return Squad
   */
  public function squad()
  {
    return $this->post->squad;
  }
}
