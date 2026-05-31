<?php

namespace Heiakim\Model\Squad;

use Heiakim\Justin;
use Heiakim\Application\Exception;
use Heiakim\Application\Logger;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Model\Notification;
use Heiakim\Model\Squad;
use Heiakim\Model\User;

class SquadRequest extends Justin
{

  /**
   * @var string
   */
  protected $table = "clan_requests";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "clan_id",
    "type",
    "reference_id",
    "done_at",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @param $params
   * @return object
   */
  public function new(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * The user being invited from another member of the squad.
     * @var ?User
     */
    $InvitedUser = $params->InvitedUser;

    /**
     * CurrentUser tries to invite themselve?
     */
    if ($CurrentUser->is($InvitedUser))
      return $this->error("<strong>🤣</strong> WhUpSiE …");

    /**
     * @var Squad
     */
    $Squad = $params->Squad;

    /**
     * ? Invitation
     */
    if ($params->type === "invite") {

      /**
       * If the type is an invite, check for the current user being
       * allowed to add new members to the squad.
       */
      if (!$CurrentUser->sqcan("coordinate", "users"))
        return $this->error("<strong>No PeRmIsSiOnS aSsSs</strong> 🍑");

      /**
       * User is socially excluded?
       */
      if ($InvitedUser->is_socially_excluded())
        return $this->error("<strong>This player is currently excluded from social interactions.</strong>");

      /**
       * User is in a squad by now?
       */
      if ($InvitedUser->has_squad())
        return $this->error("<strong>This player is currently in a squad.</strong> Support for poaching other members will be implemented soon!");
    }

    /**
     * ? Join
     */
    else if (!$CurrentUser->sqcan_request($Squad))
      return $this->error("<strong>No join right now fren.</strong> Maybe l8er!");

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Make a request.
       */
      $Request = $CurrentUser
        ->squad_requests()
        ->make([
          "clan_id" => $Squad->id,
          "type" => $InvitedUser ? "invite" : "join",
          "reference_id" => $InvitedUser?->id ?? $CurrentUser->id,
        ]);

      /**
       * Save all & commit.
       */
      $Request->save();
      $this->db_commit();

      return $this->success(
        $params->type === "invite"
          ? "<strong>»" . $InvitedUser->name . "« has been invited to join your squad!</strong> Good luck."
          : "<strong>Your request has been sent!</strong> Good luck!"
      );
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
   * @param $params
   * @return object
   */
  public function accept(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var User
     */
    $JoiningUser = $this->affected_user;

    /**
     * Poaching is not yet implemented so reject the JoiningUser
     * from joining if they have a squad at the moment.
     */
    if ($JoiningUser->has_squad())
      return $this->error("<strong>In a squad already!</strong> Poaching :soon: ~tm~");

    /**
     * ? Invitation
     */
    if ($this->type === "invite") {

      /**
       * When it's an accepted invite, the current user has to be
       * the same as the joining user.
       */
      if (!$CurrentUser->is($JoiningUser))
        return $this->error();
    }

    /**
     * ? Join
     */
    else {

      /**
       * Current User has permissions to accept the request?
       */
      if (!$CurrentUser->sqcan("coordinate", "users"))
        return $this->error("!NO_PERMISSIONS");

      /**
       * Request belongs to the current user's squad?
       */
      if (!$CurrentUser->squad->is($this->squad))
        return $this->error();
    }

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * @var int
       */
      $member_privs = SquadPrivilege::MEMBER->value + SquadPrivilege::UNRESTRICTED->value;

      /**
       * @var Notification
       */
      $Notification = $JoiningUser->notifications()
        ->make([
          "type" => "__clan__/request/accepted",
          "reference_id" => $this->squad->id,
          "reference_2_id" => $CurrentUser->id,
        ]);

      /**
       * @var Log
       */
      $Log = $this->squad
        ->feed_items()
        ->make([
          "user_id" => $CurrentUser->id,
          "type" => "__member__/joined",
          "reference_id" => $JoiningUser->id,
        ]);

      /**
       * Set the values to update for the joining user.
       */
      $JoiningUser->clan_id = $this->squad->id;

      /**
       * @var SquadUser
       */
      $SquadUser = $JoiningUser
        ->squad_user()
        ->make([
          "clan_id" => $this->squad->id,
          "clan_priv" => $member_privs,
        ]);

      /**
       * Save all & commit.
       */
      $JoiningUser->save();
      $SquadUser->save();
      $Notification->save();
      $Log->save();

      /**
       * Delete all requests of this user as we don't want another
       * pending request to be accepted and pulling the user out
       * of the new squad. This is something for the future.
       */
      $JoiningUser->squad_requests
        ->each(fn($R) => $R->delete());

      /**
       * Commit.
       */
      $this->db_commit();

      return $this->success("<strong>Sent!</strong>");
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
   * @param $params
   * @return object
   */
  public function edit(object $params) {}

  /**
   * @param $params
   * @return object
   */
  public function remove(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * Current user has permissions to coordinate users?
     */
    if (!$this->affected_user->is($CurrentUser) && !$CurrentUser->sqcan("coordinate", "users"))
      return $this->error("!NO_PERMISSIONS");

    /**
     * Request doesn't belong to the squad of the current user?
     */
    if (!$this->affected_user->is($CurrentUser) && !$this->squad->is($CurrentUser->squad))
      return $this->error("aaaaaaaaaaaa");

    try {

      /**
       * Send a notification to the affected user if it was a join request.
       */
      if (!$this->affected_user->is($CurrentUser))
        $this->affected_user
          ->notifications()
          ->create([
            "type" => "__clan__/request/declined",
            "reference_id" => $this->squad->id,
            "reference_2_id" => $CurrentUser->id,
          ]);

      /**
       * Delete & commit.
       */
      $this->delete();
      $this->db_commit();

      return $this->success("<strong>Sent!</strong>");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error($e);
    }
  }

  /**
   * User that triggered the request.
   *
   * @return ?User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * User that has been affected by the request, e. g. being invited.
   *
   * @return ?User
   */
  public function affected_user()
  {
    return $this->belongsTo(User::class, "reference_id", "id");
  }

  /**
   * @return ?Squad
   */
  public function squad()
  {
    return $this->belongsTo(Squad::class, "clan_id", "id");
  }
}
