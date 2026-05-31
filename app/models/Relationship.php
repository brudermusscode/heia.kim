<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Application\Exception;
use Heiakim\Application\Logger;
use Heiakim\Model\User;

class Relationship extends Justin
{

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
     * @var User
     */
    $User = $params->User;

    /**
     * Already following?
     */
    if ($CurrentUser->follows($User))
      return $this->error("<strong>You are already following this player!</strong>");

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Create it!
       */
      $CurrentUser
        ->followings()
        ->attach($User, ["updated_at" => null]);

      /**
       * Send notification.
       */
      $User
        ->notifications()
        ->create([
          "type" => "__relationship__/follow",
          "reference_id" => $CurrentUser->id,
          "updated_at" => null
        ]);

      /**
       * Commit all database transaction changes.
       */
      $this->db_commit();

      return $this->success("<strong>You are now following <a href='/u/" . $User->id . "'>&nbsp;" . $User->name . " &nbsp; <i class='ri-link-unlink'></i></a></strong>");
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
   * @return User
   */
  public function user_sent()
  {
    return $this->belongsTo(User::class, 'user1');
  }

  /**
   * @return User
   */
  public function user_received()
  {
    return $this->belongsTo(User::class, 'user2');
  }
}
