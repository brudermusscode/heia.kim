<?php

namespace Heiakim\Model\Squad;

use Heiakim\Justin;
use Heiakim\Application\Exception;
use Heiakim\Application\Logger;
use Heiakim\Authorization\SquadGuard;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Model\Comment;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Image;
use Heiakim\Utils\Arr;

class SquadUser extends Justin
{

  /**
   * @var string
   */
  protected $table = "clan_users";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "clan_id",
    "clan_priv",
    "performance",
    "updated_at",
  ];

  /**
   * @var array
   */
  protected $attributes = [
    "id" => 0,
    "user_id" => 0,
    "clan_id" => 0,
    "clan_priv" => 0,
    "performance" => 0,
    "updated_at" => null,
  ];

  /**
   * @var array
   */
  protected static $performance_parts = [
    "performance" => 0,
    "accuracy" => 0.00,
    "total_score" => 0,
    "ranked_score" => 0,
    "plays" => 0,
  ];

  /**
   * @var int 1 = 100%.
   */
  public static $contribution_factor = 0.03;

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
     * @var ?SquadRequest
     */
    $Request = $params->Request;

    /**
     * WIll only be set if a Request exists and the SquadUser is
     * being created by an accept of any Request.
     * @var ?SquadRequest
     */
    $RequestUser = $params->RequestUser;

    /**
     * @var Squad
     */
    $Squad = $params->Squad;

    /**
     * @var Notification[]
     */
    $Notifications = [];

    /**
     * @var SquadFeedItem[]
     */
    $Logs = [];

    /**
     * The user that is joining the squad. Will be set to the
     * correct one in the following code.
     * @var ?User
     */
    $JoiningUser = null;

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
       * @var User
       */
      $JoiningUser = $RequestUser ?? $CurrentUser;

      /**
       * No joining user has been set, which should never
       * happen but WHO KNOWS.
       */
      if (!$JoiningUser)
        return $this->error();

      /**
       * Create a log for the squad's feed.
       */
      $Logs[] = $Squad->feed_items()
        ->make([
          "user_id" => $CurrentUser->id,
          "type" => "__member__/joined",
          "reference_id" => $JoiningUser->id,
        ]);

      /**
       * Set the values to update for the joining user.
       */
      $JoiningUser->clan_id = $Squad->id;

      /**
       * Add a SquadUser to the CurrentUser.
       */
      $SquadUser = $JoiningUser
        ->squad_user()
        ->make([
          "clan_id" => $Squad->id,
          "clan_priv" => $member_privs,
        ]);

      /**
       * Save all & commit!
       */
      $JoiningUser->save();
      $SquadUser->save();

      foreach ($Logs as $Log)
        $Log->save();

      foreach ($Notifications as $Notification)
        $Notification->save();

      $Request?->delete();

      $this->db_commit();

      return $this->success("<strong>Let's go!</strong> You are in.");
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

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var Notification[]
     */
    $Notifications = [];

    /**
     * @var SquadFeedItem[]
     */
    $Logs = [];

    /**
     * ? Restrict
     * Return the restriction function as this is by now a
     * function standing for its own. No other parameter of the
     * squad user should be changed in addition. We are checking
     * whether the clan_priv only contains a number of 2 (MEMBER)
     * or an array only containing one item with the key of MEMBER
     * value.
     */
    if (
      !empty($params->clan_priv)
      && (
        $params->clan_priv == 2
        || is_array($params->clan_priv)
        && count($params->clan_priv) === 1
        && array_key_first($params->clan_priv) === SquadPrivilege::MEMBER->value
      )
    )
      return $this->restrict($params);

    /**
     * ? Unrestrict/Setfree
     */
    if (
      !empty($params->clan_priv)
      && (
        $params->clan_priv == 6
        || is_array($params->clan_priv)
        && count($params->clan_priv) === 2
        && array_key_first($params->clan_priv) === SquadPrivilege::MEMBER->value
        && array_key_last($params->clan_priv) === SquadPrivilege::UNRESTRICTED->value
      )
    )
      return $this->unrestrict($params);

    /**
     * ? Privileges
     */
    if (!empty($params->clan_priv) && is_array($params->clan_priv)) {

      /**
       * Check if the CurrentUser has permissions to edit the
       * permissions of this SquadUser's instance.
       */
      if (
        // CurrentUser can edit this instance's permissions?
        !$CurrentUser->squad_user->can_edit_permissions_of($this)
        // Tries to give chief grade without being chief?
        || !$CurrentUser->is_squad_chief() && isset($params->clan_priv[SquadPrivilege::CHIEF->value]) && $params->clan_priv[SquadPrivilege::CHIEF->value]
        // Tries to give community manager grade without being chief?
        || !$CurrentUser->is_squad_chief() && isset($params->clan_priv[SquadPrivilege::COMMUNITY_MANAGER->value]) && $params->clan_priv[SquadPrivilege::COMMUNITY_MANAGER->value]
      )
        return $this->error("!NO_PERMISSIONS");

      /**
       * @var int
       */
      $new_privs = SquadPrivilege::MEMBER->value + SquadPrivilege::UNRESTRICTED->value;

      /**
       * Add together all bits from clan_priv array.
       */
      foreach ($params->clan_priv as $bits => $enabled) :

        /**
         * Check if the Current User is community manager and tries
         * to set a new community manager.
         */
        if (
          $CurrentUser->is_squad_community_manager()
          && $bits === SquadPrivilege::COMMUNITY_MANAGER->value
          && $enabled
        )
          return $this->error("!NO_PERMISSIONS");

        $new_privs += $enabled ? $bits : 0;
      endforeach;

      /**
       * If no new privileges are set, just add up the old ones to
       * prevent any changes.
       */
      $params->clan_priv = $new_privs ?: $this->clan_priv;

      /**
       * Return the promote function if there are new privs.
       */
      if ($params->clan_priv !== $this->clan_priv)
        return $this->promote($params);
    }

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();
    try {

      /**
       * Save it!
       */
      $this->save();

      /**
       * Save all notifications.
       */
      foreach ($Notifications as $Notification)
        $Notification->save();

      /**
       * Save all logs.
       */
      foreach ($Logs as $Log)
        $Log->save();

      /**
       * Commit!
       */
      $this->db_commit();

      return $this->success("<strong>Updated!</strong>");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error("!TRY_OR_STAFF");
    }
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params)
  {

    /**
     * The user that removes the user from the squad. Could be
     * themselves, if they are leaving.
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * The user that has to leave the squad.
     * @var User
     */
    $LeavingUser = $this->user;

    /**
     * @var bool
     */
    $is_same_user = $CurrentUser->is($LeavingUser);

    /**
     * @var Squad
     */
    $Squad = $this->squad;

    /**
     * @var Notification[]
     */
    $Notifications = [];

    /**
     * @var SquadFeedItem[]
     */
    $Logs = [];

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * Leaving user is chief and it has more than one member
       * including restricted ones?
       */
      if ($this->is_chief() && (($Squad->members_count() + $Squad->restricted_members_count()) > 1))
        return $this->error("<strong>You can't leave your squad as a chief.</strong> <a href='/manage/squad/leave'>Read more about it &nbsp; <mi smol>open_in_new</mi></a>");

      /**
       * Leaving user is the chief but it's the only member in the
       * squad. In this case, we will delete the squad and return.
       */
      else if ($this->is_chief())
        return $Squad->remove();

      /**
       * Create a log.
       */
      $Logs[] = $Squad->feed_items()
        ->make([
          "user_id" => $CurrentUser->id,
          "reference_id" => $LeavingUser->id,
          "type" => !empty($params->is_kick) ? "__member__/kicked" : "__member__/left",
        ]);

      /**
       * In case the leaving user has been removed from
       * another member with elevated staff grade, send a
       * notification to the leaving user.
       */
      if (!$is_same_user)
        $Notifications[] = $LeavingUser->notifications()
          ->make([
            "reference_id" => $CurrentUser->id,
            "reference_2_id" => $Squad->id,
            "type" => "__clan__/user/removed",
          ]);

      /**
       * Update leaving user's clan to 0.
       */
      $LeavingUser->clan_id = 0;

      /**
       * Save all & commit.
       */
      $LeavingUser->save();

      foreach ($Notifications as $Notification)
        $Notification->save();

      foreach ($Logs as $Log)
        $Log->save();

      $Squad->update_performance([$this->id]);

      $this->posts()
        ->each(function ($Post) {
          $Post->remove();
        });

      /**
       * Delete & commit!
       */
      $this->delete();
      $this->db_commit();

      return $this->success("<strong>Say goodbye!</strong>");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error($e->getMessage());
    }


    /**
     * ? SAME USERS
     */



    /**
     * Delete posts & comments on the posts. We keep the
     * comments on other posts for context.
     */
    foreach ($this->posts as $P) {
      $P->comments()
        ->delete();

      $P->delete();
    }

    /**
     * Update the squads performance.
     */
    $Squad->update_performance([$this->id]);
  }

  /**
   * @return mixed
   */
  public function performance()
  {
    return json_decode($this->performance);
  }

  /**
   * @return object
   */
  public function performance_per_mode()
  {
    $performance = $this->performance();
    $new_performance = [
      "osu" => self::$performance_parts,
      "ctb" => self::$performance_parts,
      "taiko" => self::$performance_parts,
      "mania" => self::$performance_parts,
    ];

    /**
     * Only iterate, if the user has already achieved any
     * performance for the squad.
     */
    if ($performance)
      foreach ($performance as $gumode => $p) {
        $mode_text = Gamemode::convert_gumode_to_mode_text($gumode);

        if ($mode_text)
          foreach ($p as $name => $value)
            $new_performance[$mode_text][$name] += $value;
      }

    return Arr::objectify($new_performance);
  }

  /**
   * Calculates the base performances for each active game mode
   * from the user attached to the squad user and saves it to the database.
   *
   * @return void
   */
  public function update_performance()
  {
    /**
     * @var array
     */
    $stats = [
      0 => [],
      1 => [],
      2 => [],
      3 => [],
      4 => [],
      5 => [],
      6 => [],
      8 => [],
    ];

    /**
     * @var Stat
     */
    $Stats = $this->user
      ->stats;

    /**
     * Calculate the performance which will be contributet to
     * the squads bank account.
     */
    foreach (Gamemode::$mods_int_per_mode as $mode => $gumodes) {

      /**
       * @var object
       */
      $squad_modes = $this->squad->modes;

      /**
       * Skip if the mode is disabled.
       */
      if ($squad_modes[$mode] == 0)
        continue;

      /**
       * Iterate through all the gumodes inside the mode array
       * and calculate their performances and add them together
       * for each mode.
       */
      foreach ($gumodes as $gumode) {

        /**
         * @var Stat Stats filtered by the current gumode.
         */
        $StatMode = $Stats->filter(function ($Stat) use ($gumode) {
          return $Stat->mode == $gumode;
        })
          ->values()
          ->first();

        /**
         * Append the calculated stats.
         */
        $stats[$gumode] = [
          "performance" => $StatMode->pp * self::$contribution_factor,
          "accuracy" => $StatMode->acc,
          "total_score" => $StatMode->tscore * self::$contribution_factor,
          "ranked_score" => $StatMode->rscore * self::$contribution_factor,
          "plays" => 0,
        ];
      }
    }

    /**
     * Set it!
     */
    $this->performance = Arr::to_json($stats);
    $this->save();

    return;
  }

  /**
   * @param object $params
   * @return object
   */
  public function promote(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var int
     */
    $old_priv_bits = (int) $this->clan_priv;

    /**
     * @var array
     */
    $NewPrivileges = [];

    /**
     * Create a log, if the user was set free from restriction,
     * which includes MEMBER & UNRESTRICTED privileges combined.
     */
    if ($params->clan_priv === (SquadPrivilege::MEMBER->value + SquadPrivilege::UNRESTRICTED->value))
      $Logs[] = $this->squad
        ->feed_items()
        ->make([
          "user_id" => $CurrentUser->id,
          "type" => "__member__/setfree",
          "reference_id" => $this->user->id,
          "updated_at" => null,
        ]);

    /**
     * Add all new privileges together into one array and skip
     * certain ones, that shouldn't be addable/removable.
     */
    foreach (SquadPrivilege::cases() as $Privilege)
      if (
        (($params->clan_priv & $Privilege->value) !== 0)
        && !in_array($Privilege, $NewPrivileges)
        && !in_array($Privilege, [SquadPrivilege::CHIEF, SquadPrivilege::MEMBER, SquadPrivilege::UNRESTRICTED])
      )
        $NewPrivileges[] = $Privilege;

    /**
     * @var int
     */
    $this->clan_priv = SquadPrivilege::MEMBER->value + SquadPrivilege::UNRESTRICTED->value;

    /**
     * Begin with the basic Member privileges and add
     * unrestricted.
     */
    foreach ($NewPrivileges as $Privilege)
      $this->clan_priv += $Privilege->value;

    /**
     * @var string
     */
    $notification_type = "__clan__/user/edit+priv+" . ($old_priv_bits < $this->clan_priv ? "increase" : "decrease");

    /**
     * Append a notification.
     */
    $Notifications[] = $this->user
      ->notifications()
      ->make([
        "type" => $notification_type,
        "reference_id" => $CurrentUser->id,
        "reference_2_id" => $this->clan_priv,
        "updated_at" => null,
      ]);

    /**
     * Append a log.
     */
    if ($old_priv_bits >= (SquadPrivilege::MEMBER->value + SquadPrivilege::UNRESTRICTED->value))
      $Logs[] = $this->squad
        ->feed_items()
        ->make([
          "user_id" => $CurrentUser->id,
          "type" => "__member__/" . ($old_priv_bits < $this->clan_priv ? "promoted" : "demoted"),
          "reference_id" => $this->user->id,
          "updated_at" => null,
        ]);

    try {

      /**
       * Save all notifications.
       */
      foreach ($Notifications ?? [] as $Notification)
        $Notification->save();

      /**
       * Save all logs.
       */
      foreach ($Logs ?? [] as $Log)
        $Log->save();

      /**
       * Save the Member & commit!
       */
      $this->save();
      $this->db_commit();

      return $this->success("<strong>Member has been updated & notified!</strong>");
    } catch (\Exception $e) {

      /**
       * Log & rollback.
       */
      Logger::to_file($e);
      $this->db_rollback();

      return $this->error("!TRY_OR_STAFF");
    }
  }

  /**
   * @param object $params
   * @return object
   */
  public function restrict(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * If the user to restrict is of any higher privilege than
     * just member, only the squad chief can restrict them.
     */
    if (!$CurrentUser->is_squad_chief() && $this->has_elevated_privileges())
      return $this->error("!NO_PERMISSIONS");

    /**
     * Transaction opening & try catch.
     */
    $this->db_transaction();
    try {

      /**
       * Set the new privileges to just the member. Taking
       * UNRESTRICTED will restrict the member.
       */
      $this->clan_priv = SquadPrivilege::MEMBER->value;

      /**
       * Create a notification.
       */
      $this->user
        ->notifications()
        ->create([
          "type" => "__clan__/user/restricted",
          "reference_id" => $CurrentUser->id,
          "reference_2_id" => $this->squad->id,
          "updated_at" => null,
        ]);

      /**
       * Create a log.
       */
      $this->squad
        ->feed_items()
        ->create([
          "user_id" => $CurrentUser->id,
          "type" => "__member__/restricted",
          "reference_id" => $this->user->id,
          "updated_at" => null,
        ]);

      /**
       * Save & commit!
       */
      $this->save();
      $this->db_commit();

      return $this->success("<strong>The member has been restricted and notified!</strong>");
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
   * @param SquadUser $SquadUser
   * @return bool
   */
  public function can_restrict(SquadUser $SquadUser)
  {
    return !$this->is($SquadUser)
      && (
        $this->can("coordinate", "users") && !$SquadUser->has_elevated_privileges()
        || $this->can("manage", "users") && !$SquadUser->can("manage", "users")
        || $this->is_chief() && !$SquadUser->is_chief()
      );
  }

  /**
   * @param SquadUser $SquadUser
   * @return bool
   */
  public function can_edit_permissions_of(SquadUser $SquadUser)
  {
    return !$this->is($SquadUser)
      && (
        $this->can("manage", "users") && !$SquadUser->can("manage", "users")
        || $this->is_chief() && !$SquadUser->is_chief()
      );
  }

  /**
   * @param object $params
   * @return object
   */
  public function unrestrict(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * If the user to restrict is of any higher privilege than
     * just member, only the squad chief can restrict them.
     */
    if (!$CurrentUser->sqcan("coordinate", "users"))
      return $this->error("!NO_PERMISSIONS");

    /**
     * Transaction opening & try catch.
     */
    $this->db_transaction();
    try {

      /**
       * @var int
       */
      $this->clan_priv = SquadPrivilege::MEMBER->value + SquadPrivilege::UNRESTRICTED->value;

      /**
       * Create a notification.
       */
      $this->user
        ->notifications()
        ->create([
          "type" => "__clan__/user/setfree",
          "reference_id" => $CurrentUser->id,
          "reference_2_id" => $this->squad->id,
          "updated_at" => null,
        ]);

      /**
       * Create a log.
       */
      $this->squad
        ->feed_items()
        ->create([
          "user_id" => $CurrentUser->id,
          "type" => "__member__/setfree",
          "reference_id" => $this->user->id,
          "updated_at" => null,
        ]);

      /**
       * Save & commit!
       */
      $this->save();
      $this->db_commit();

      return $this->success("<strong>The member has been set free and notified!</strong>");
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
   * @return ?SquadPost
   */
  public function posts()
  {
    return $this->squad
      ->posts()
      ->where("user_id", $this->user_id);
  }

  /**
   * @return ?SquadPost
   */
  public function comments()
  {
    return $this->squad
      ->posts()
      ->comments()
      ->where("user_id", $this->user_id);
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, DISPLAY ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @return include /app/templates/helper/users/_image.php
   */
  public function image()
  {
    return $this->user->image();
  }

  /**
   * @return string
   */
  public function name()
  {
    return $this->user->name();
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
    return $this->belongsTo(Squad::class, "clan_id", "id");
  }

  /**
   * @return bool
   */
  public function is_owner()
  {
    return $this->has_privileges_of(SquadPrivilege::CHIEF);
  }

  /**
   * @return bool
   */
  public function is_chief()
  {
    return $this->is_owner();
  }

  /**
   * @return bool
   */
  public function is_community_manager()
  {
    return $this->has_privileges_of(SquadPrivilege::COMMUNITY_MANAGER);
  }

  // ? >>>>>>>>>>>>>>>>>>>>>> PRIVILEGES >>>>>>>>>>>>>>>>>>>>>>>>>>>>

  /**
   * @return array[SquadPrivilege]
   */
  public function privileges()
  {
    $privileges = [];

    foreach (SquadPrivilege::cases() as $bits => $Privilege) {
      $display = $Privilege->get_display();

      if (($this->clan_priv & $Privilege->value) !== 0) {
        $privileges[] = (object) [
          "privilege" => $Privilege,
          "bits" => $bits,
          "name" => $display->name,
          "icon" => $display->icon,
        ];
      }
    }

    return $privileges ? $privileges : null;
  }

  /**
   * @return SquadPrivilege
   */
  public function highest_privileges()
  {
    return $this->privileges()[0];
  }

  /**
   * @return SquadPrivilege
   */
  public static function highest_privileges_from(int $bits)
  {
    $Privileges = [];

    foreach (SquadPrivilege::cases() as $Privilege) {
      if (($bits & $Privilege->value) !== 0)
        $Privileges[] = $Privilege;
    }

    return $Privileges[0];
  }

  /**
   * Checks for only one specific privilege being available on
   * this instance.
   *
   * @param SquadPrivilege $Privilege
   * @return bool
   */
  public function has_privileges_of(SquadPrivilege $Privilege)
  {
    return ($this->clan_priv & $Privilege->value) !== 0;
  }

  /**
   * Checks if any of the given privileges are available on this instance.
   *
   * @param SquadPrivilege $Privileges
   * @return bool
   */
  public function has_any_privileges_of(SquadPrivilege ...$Privileges)
  {
    foreach ($Privileges as $Privilege)
      if (($this->clan_priv & $Privilege->value) !== 0)
        return true;

    return false;
  }

  /**
   * If the user has any other privileges than being a member and
   * unrestricted.
   *
   * @return bool
   */
  public function has_elevated_privileges()
  {
    return count($this->privileges()) > 2
      && $this->highest_privileges() !== SquadPrivilege::ASSISTANT
      && !$this->is_restricted();
  }



  /**
   * Check if the SquadUser has permissions higher than the given one.
   *
   * @param SquadPrivilege $Privilege
   * @return bool
   */
  public function has_higher_privileges_than(SquadPrivilege $Privilege)
  {
    $has_reached = false;
    $Privileges = [];

    foreach (array_reverse(SquadPrivilege::cases()) as $P) {
      if ($P !== $Privilege && !$has_reached)
        continue;

      $has_reached = true;

      $Privileges[] = $P;
    }

    unset($Privileges[0]);

    if (in_array($this->highest_privileges()->privilege, $Privileges))
      return true;

    return false;
  }

  /**
   * @return bool
   */
  public function is_restricted()
  {
    return ($this->clan_priv & SquadPrivilege::UNRESTRICTED->value) === 0;
  }

  /**
   * @param Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content
   * @return bool
   */
  public function can_touch(Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content)
  {
    return $this->user->is_super_user()
      || $Content->squad?->is($this->squad)
      && (
        $this->user->is($Content->user)
        || $this->is_chief()
        || (
          $this->can("manage", "content")
          && !$Content->user->sqcan("manage", "content")
          &&
          (
            !$Content->user->squad_user?->is_chief()
            || !$Content->user->squad_user?->is_community_manager()
          )
        )
      );
  }

  /**
   * Function to use in Requests to authorize the editing or deleting
   * with a certain content type and immediately die on error.
   *
   * @param Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content
   * @return bool
   */
  public function authorize_content_touch(Image|Comment|SquadFeedItem|SquadPost|SquadPostComment|SquadPostPollAnswer $Content, $die_on_error = true)
  {
    return !$this->can_touch($Content)
      ? request_error("!NO_PERMISSIONS", die: $die_on_error)
      : true;
  }

  /**
   * Check if the SquadUser has permissions to interact with a
   * certain content type like commenting or feedback giving.
   *
   * @param SquadFeedItem|SquadPost $Content
   * @return bool
   */
  public function can_interact_with(SquadFeedItem|SquadPost $Content)
  {
    return $this->user->is_super_user()
      || ($this->squad->is($Content->squad)
        && (
          !$this->is_socially_excluded()
        )
      );
  }

  /**
   * Function to use in Requests to authorize the interaction
   * with a certain content type and immediately die on error.
   *
   * @param SquadFeedItem|SquadPost $Content
   * @return bool
   */
  public function authorize_content_interaction(SquadFeedItem|SquadPost $Content, $die_on_error = true)
  {
    return !$this->can_interact_with($Content)
      ? request_error("!NO_PERMISSIONS", die: $die_on_error)
      : true;
  }

  /**
   * Check if the SquadUser has permissions to interact with a
   * certain Squad, like commenting or creating posts.
   *
   * @param Squad $Squad
   * @return bool
   */
  public function can_take_action_in(Squad $Squad)
  {
    return $this->user->is_super_user()
      || (
        $this->squad->is($Squad)
        && !$this->is_socially_excluded()
      );
  }

  /**
   * Checks wether this squad user can leave their squad. Takes in
   * account if it's a chief for example.
   *
   * @return bool
   */
  public function can_leave()
  {
    // TODO: Make it so it can be used in what_prevents_leaving().
    return !$this->is_chief();
  }

  /**
   * Checks wether this squad user can leave their squad. Takes in
   * account if it's a chief for example.
   *
   * @return ?string
   */
  public function what_prevents_leaving()
  {
    return match (true) {
      $this->is_chief() => "<strong>You can't leave your squad as a chief.</strong> <a href='/manage/squad/leave'>Read more about it &nbsp; <mi smol>open_in_new</mi></a>",
      default => null,
    };
  }

  /**
   * Check for user's restriction status in their current squad or
   * delegate to the social exclusion checker function of the
   * attached user.
   *
   * @return bool
   */
  public function is_socially_excluded()
  {
    return $this->is_restricted() || $this->user->is_socially_excluded();
  }

  /**
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,, PERMISSION GUARD ,,,,,,,,,,,,,,,,,,,,
   * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
   */

  /**
   * @uses Authorization\SquadGuard
   * @param string $interaction
   * @param string $section
   * @return bool
   */
  // TODO: Make section in can() optional.
  public function can(string $interaction, string $section, ?Squad $in = null)
  {
    $Privileges = $this->privileges();

    /**
     * Super user allowed to do anything.
     */
    if ($this->user->is_super_user())
      return true;

    /**
     * Check if the checked on user is in the squad given by the
     * params. If not, return false. Only super users should be
     * able to manage squads outside of their own, or without
     * having any.
     */
    if ($in && !$this->user->squad?->is($in))
      return false;

    if (is_array($Privileges) && count($Privileges) > 1) {
      foreach ($Privileges as $Privilege) {
        if (SquadGuard::can($interaction, $section, $Privilege->privilege))
          return true;
      }

      return false;
    } else
      return SquadGuard::can($interaction, $section, $Privileges);
  }
}
