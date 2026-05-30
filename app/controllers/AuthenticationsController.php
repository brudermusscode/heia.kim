<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\Authentication;

class AuthenticationsController extends Controller
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
      optional: ["email", "value"],
    );

    /**
     * If a new user tries to sign up, we need to check if the
     * user is not already logged in nor does the new email
     * address can be missing.
     */
    if ($this->params->type === "user:create" && $this->CurrentUser)
      return request_error("!ALREADY_LOGGED");

    /**
     * Validate a mail is set if it's a new user requesting to
     * sign up.
     */
    if ($this->params->type === "user:create" && !$this->params->email)
      return request_error("<strong>Bro, where mail?</strong>");

    /**
     * User unlogged in any other case?
     */
    if ($this->params->type !== "user:create" && !$this->CurrentUser)
      return request_error("!NOT_LOGGED");

    return (new Authentication)->new($this->params);
  }

  /**
   * PUT
   *
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["token"],
      optional: ["email"],
    );

    /**
     * User is not logged in?
     */
    $this->authorize(false);

    /**
     * @var ?Authentication
     */
    $Authentication = Authentication::where("token", $this->params->token)
      ->first();

    /**
     * Authentication doesn't exist?
     */
    if (!$Authentication)
      return $this->error();

    return $Authentication->edit($this->params);
  }
}
