<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Session;
use Heiakim\Application\Feature;
use Heiakim\Model\User;

class SessionsController extends Controller
{

  /**
   * @return object
   */
  public function create()
  {

    # Feature disabled?
    if (!Feature::is_enabled("login"))
      return error("!FEATURE_DISABLED");

    $this->validate_params(
      strict: ["login", "password"],
      optional: [],
    );

    $this->authorize(logged: false);

    /**
     * @var ?User
     */
    $User = User::verify_login(
      $this->params->login,
      $this->params->password,
      die: true
    );

    # Create a new Session.
    $Session = (new Session)->new($User);

    return success(data: $Session);
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["token"],
      optional: [],
    );

    $this->authorize();

    /**
     * @var ?Session
     */
    $Session = CurrentUser->sessions()
      ->where("token", $this->params->token)
      ->first();

    # Session doesn't exist or is deleted already?
    if (!$Session) return error();

    # As Users can have more than one Session on different
    # devices, they can delete one that is not the current
    # one. So not every Session deletion is a logout.
    $is_current_session = SESSION->is($Session);

    # Clean up anything related to a current session if the
    # User has logged out.
    if ($is_current_session)
      $Session::clean_up();

    # Delete the Session and clean up every relation.
    $Session->delete();

    return success(data: ["is_current_session" => $is_current_session]);
  }
}
