<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\User;
use Heiakim\Model\Authentication;
use Heiakim\Model\ConnectionOsu;
use Heiakim\Model\Session;

class UsersController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["name", "password", "token", "email"],
      optional: [],
    );

    $this->authorize(logged: false);

    /**
     * @var Authentication
     */
    $Authentication = Authentication::with("connection")
      ->where("token", $this->params->token)
      ->whereNull("deleted_at")
      ->first();

    # No authentication found?
    if (!$Authentication)
      return $this->error("<strong>Authentication not found.</strong>");

    /**
     * @var ?ConnectionOsu
     */
    $Connection = $Authentication->connection;

    # The email can either come from an API Connection (high priority) or the Authen-
    # tication itself. If none of these have an email set, the User will in any other
    # case send one with the form parameters. This ensures that the mail from previous
    # authentication steps will be used.
    $this->params->email = $Connection?->email
      ?? $Authentication->email
      ?? $this->params->email;

    # Append some other parameter from a possible API Connection.
    $this->params->is_legit = $Connection?->is_legit;

    # TODO: Append a picture from API Connection.
    $this->params->files = null;

    # Create a new User.
    $User = (new User)->new($this->params);

    # Associate the new User with an API Connection if one shall exist.
    $Connection?->user()
      ->associate($User)
      ->save();

    # Delete the Authentication.
    $Authentication->delete();

    # Create a new Session.
    new Session()->new($User);

    return success("<strong>You in!</strong> Have fun on your journey.");
  }

  /**
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: [],
      optional: ["name", "mode", "mod", "email", "code", "token", "password", "current_password"],
    );

    $this->authorize();

    CurrentUser->edit($this->params);

    return success("<strong>Saved!</strong>");
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->authorize();

    CurrentUser->remove();

    return success("You are gone! <strong>Jesus loves you!</strong> 🙂");
  }

  /**
   * @return string
   */
  public function wipe()
  {

    $this->authorize();

    CurrentUser->wipe();

    return success("All gone! Have fun with your fresh start! 🫰");
  }
}
