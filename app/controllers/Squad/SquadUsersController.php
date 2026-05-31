<?php

namespace Heiakim\Controller\Squad;

use Heiakim\Controller\Controller;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Enum\SquadPrivilege;

class SquadUsersController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id"],
      optional: ["user_id"]
    );

    /**
     * User is authorized?
     */
    $this->authorize();

    /**
     * @var ?Squad
     */
    $Squad = Squad::findOrReturn($this->params->id, "<strong>This squad doesn't exist!</strong> It might have been deleted.");

    /**
     * If a user id isset, it's a request that has been accepted.
     */
    if (!empty($this->params->user_id)) {

      /**
       * @var ?User
       */
      $User = User::findOrReturn($this->params->user_id, "<strong>This player doesn't exist or is restricted.</strong>");

      /**
       * User can join a new squad?
       */
      if (!$User->sqcan_join($Squad))
        return $this->error("<strong>This player can't join a squad right now.</strong> Maybe l8er boi.");

      /**
       * User is current user?
       */
      if (CurrentUser->is($User))
        return $this->error("???????");

      /**
       * @var ?SquadRequest
       */
      $Request = $Squad->requests()
        ->whereOr("reference_id", $User->id) // invitation request
        ->first();

      /**
       * Requests exists?
       */
      if (!$Request)
        return $this->error("<strong>There is no pending request for this player to join your squad.</strong>");

      /**
       * Authorize current user to coordinate new members.
       */
      $this->authorize(
        resource: CurrentUser->squad_user,
        can: ["coordinate", "users"],
      );
    }

    /**
     * User can join this squad?
     */
    else if (!CurrentUser->sqcan_join($Squad))
      return $this->error("<strong>You can't join this squad right now.</strong>");

    /**
     * Append variables.
     */
    $this->params->Squad = $Squad;
    $this->params->Request = $Request ?? null;
    $this->params->RequestUser = $User ?? null;

    return (new SquadUser)->new($this->params);
  }


  /**
   * DELETE
   *
   * @return string
   */
  public function kick()
  {

    $this->validate_params(
      strict: ["id"],
      optional: []
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      can: ["manage", "users"],
    );

    /**
     * @var ?SquadUser
     */
    $SquadUser = SquadUser::findOrReturn($this->params->id, "<strong>Member not found! 🫥</strong>");

    /**
     * If a user tries to remove another squaduser, check for
     * enought privileges.
     */
    if ($SquadUser->user->is(CurrentUser))
      return $this->error("<strong>NO 😑</strong>");

    /**
     * User can actually leave their squad?
     */
    if (!$SquadUser->can_leave())
      return $this->error($SquadUser->what_prevents_leaving());

    /**
     * Tell it's a kick.
     */
    $this->params->is_kick = !false;

    return $SquadUser->remove($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    /**
     * Authorized access to this controller?
     */
    $this->authorize();

    /**
     * Authenticate this user to fulfill this action.
     */
    $this->authenticate();

    /**
     * Params valid?
     */
    $this->validate_params(
      strict: [],
      optional: ["squad_user_id"]
    );

    /**
     * Either a user being removed by another user from the squad
     * or the user tries to remove themselves.
     *
     * It's the user to be removed.
     *
     * @var ?SquadUser
     */
    $SquadUser = isset($this->params->squad_user_id)
      ? SquadUser::findOrReturn($this->params->squad_user_id)
      : CurrentUser->squad_user;

    /**
     * SquadUser doesn't exist?
     */
    if (!$SquadUser)
      return $this->error("<strong>You are not part of any squad!</strong> Join one kek 💋");

    /**
     * If a user tries to remove another squaduser, check for
     * enought privileges.
     */
    if (
      !$SquadUser->user->is(CurrentUser)
      && (
        !CurrentUser->sqcan("manage", "users")
        || $SquadUser->has_any_privileges_of(SquadPrivilege::CHIEF, SquadPrivilege::COMMUNITY_MANAGER)
      )
    )
      return $this->error("<strong>No permissions fren. 🫠</strong>");

    /**
     * User can actually leave their squad?
     */
    if (!$SquadUser->can_leave())
      return $this->error($SquadUser->what_prevents_leaving());

    return $SquadUser->remove($this->params);
  }

  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id"],
      optional: ["clan_priv"]
    );

    /**
     * Authorize permissions.
     */
    // $this->squauthorize(
    //   resource: CurrentUser?->squad_user,
    //   can: ["manage", "users"],
    // );

    // Only chief can promote members to new privileges.
    // Team coordniators can set privileges to 2 (restircted), but
    // only if the member is a normal member and has no other
    // privs.

    /**
     * Evaluate, based on the given params and their values, which
     * privileges the current squad user needs to have to fiure
     * off the desired action(s).
     * @var array
     */
    $needed_privileges =
      isset($this->params->clan_priv) && $this->params->clan_priv == 2
      ? ["coordinate", "users"]
      : (
        ["manage", "users"]
      );

    /**
     * Authorize squad user.
     */
    $this->authorize(
      resource: CurrentUser?->squad_user,
      can: $needed_privileges
    );

    /**
     * @var User
     */
    $User = User::findOrReturn($this->params->id, "<strong>This player faded away!</strong>");

    /**
     * User exists & has a squad?
     */
    if (!$User->squad?->is(CurrentUser->squad))
      return $this->error("<strong>This player is not part of your squad.</strong>");

    /**
     * Trying to edit the squad chief?
     */
    if ($User->is_squad_chief())
      return $this->error("<strong>Haha sure, as if you could edit the squad chief 🤣</strong>");

    /**
     * Editing themselves in the squad should be enabled later in
     * the game, but for now I keep it disabled.
     */
    if (CurrentUser->is($User))
      return $this->error("<strong>You can't edit yourself by now.</strong>");

    return $User->squad_user->edit($this->params);
  }
}
