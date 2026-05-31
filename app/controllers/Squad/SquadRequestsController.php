<?php

namespace Heiakim\Controller\Squad;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadRequest;
use Heiakim\Model\User;

class SquadRequestsController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type"],
      optional: ["id", "user_id"],
    );

    $this->authorize();

    /**
     * @var ?Squad
     */
    $Squad = $this->params->type === "join"
      ? Squad::findOrReturn($this->params->id, "<strong>This Squad doesn't exist.</strong> It might have been deleted or set to private.")
      : CurrentUser->squad;

    /**
     * Squad exists?
     */
    if (!$Squad)
      return $this->error();

    /**
     * @var ?User
     */
    $this->params->InvitedUser =
      $this->params->type === "invite"
      ? User::findOrReturn($this->params->user_id ?? 0, "<strong>This player doesn't exist.</strong>")
      : null;

    /**
     * Append all.
     */
    $this->params->Squad = $Squad;

    return (new SquadRequest)->new($this->params);
  }

  /**
   * POST
   *
   * @return string
   */
  public function accept()
  {

    $this->validate_params(
      strict: ["id"],
      optional: [],
    );

    $this->authorize();

    /**
     * @var ?SquadRequest
     */
    $Request = SquadRequest::findOrReturn($this->params->id, "<strong>No request found.</strong> What happened?");

    return $Request->accept($this->params);
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
      optional: [],
    );

    /**
     * Current user is logged in?
     */
    $this->authorize(
      resource: CurrentUser?->squad_user,
      can: ["coordinate", "users"]
    );

    /**
     * @var SquadRequest
     */
    $Request = SquadRequest::findOrReturn($this->params->id, "<strong>There is no pending request to join this squad.</strong>");

    return $Request->edit($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"],
      optional: [],
    );

    $this->authorize();

    /**
     * @var ?SquadRequest
     */
    $Request = SquadRequest::findOrReturn($this->params->id, "<strong>No request found.</strong> What happened?");

    return $Request->remove($this->params);
  }
}
