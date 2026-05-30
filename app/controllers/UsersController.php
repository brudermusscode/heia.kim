<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Authentication;

class UsersController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["name", "password", "token"],
      optional: [],
    );


    /**
     * User is logged?
     */
    $this->authorize(logged: false);

    /**
     * @var Authentication
     */
    $Authentication =
      Authentication::where("token", $this->params->token)
      ->whereNull("deleted_at")
      ->first();

    /**
     * Authentication not found?
     */
    if (!$Authentication)
      return $this->error("<strong>Authentication not found.</strong>");

    /**
     * Append all.
     */
    $this->params->Authentication = $Authentication;
    $this->params->email = $Authentication->email;

    return (new User)->new($this->params);
  }

  /**
   * PUT
   *
   * @return string
   */
  public function update()
  {


    /**
     * Some updates of the User need to be authenticated by the
     * bad boy system. So we first authenticate the user for the
     * given type and check if everything necessary from the
     * authentication value to the type of the authentication
     * value is set.
     */
    if (get("var") === "email" && isset($this->params["authentication_value"])) {
      $this->authenticate();
      $this->params["email"] = $this->params["authentication_value"];

      unset($this->params["authentication_value"]);
    }

    $this->validate_params(
      strict: [],
      optional: ["name", "mode", "mod", "email", "code", "token", "password", "current_password"],
    );

    $this->authorize();

    return
      $this->CurrentUser
      ->edit($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->authorize();

    $this->authenticate();

    return $this->CurrentUser
      ->remove($this->params);
  }

  /**
   * Requests a wipe of the current users account.
   *
   * @return string
   */
  public function wipe()
  {

    $this->authorize();

    $this->authenticate();

    return $this->CurrentUser
      ->wipe($this->params);
  }
}
