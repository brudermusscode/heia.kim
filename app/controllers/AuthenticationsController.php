<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Authentication;

class AuthenticationsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type"],
      optional: ["email", "value"],
    );

    # For new User: logged in already?
    if ($this->params->type === "user:create" && CurrentUser->exists)
      return error("!ALREADY_LOGGED");

    # For new User: E-Mail is not set?
    if ($this->params->type === "user:create" && !$this->params->email)
      return error("<strong>Bro, where mail?</strong>");

    # Any other case: User is not logged in?
    if ($this->params->type !== "user:create" && !CurrentUser->exists)
      return error("!NOT_LOGGED");

    return (new Authentication)->new($this->params);
  }

  /**
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
      return error();

    return $Authentication->edit($this->params);
  }
}
