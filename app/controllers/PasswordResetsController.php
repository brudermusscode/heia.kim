<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\PasswordReset;

class PasswordResetsController extends Controller
{

  /**
   * @var array
   */
  protected $request_params = [
    "mail"
  ];

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["email"],
      optional: ["token", "password"],
    );

    # Starting password resets from an existing and logged in account, we can append
    # the current email address of the User.
    if (CurrentUser->exists)
      $this->params->email = CurrentUser->email ?? $this->params->email;

    return (new PasswordReset)->new($this->params);
  }

  /**
   * PUT
   *
   * @return object
   */
  public function update()
  {

    $this->validate_params(
      strict: ["token", "password"],
      optional: ["mail"],
    );

    # Starting password resets from an existing and logged in account, we can append
    # the current email address of the User.
    if (CurrentUser->exists)
      $this->params->email = CurrentUser->email ?? $this->params->email;

    /**
     * @var ?PasswordReset
     */
    $PasswordReset = PasswordReset::with("user")
      ->where("token", $this->params->token)
      ->first();

    # Token is invalid thus no PasswordReset was found?
    if (!$PasswordReset)
      return error();

    return $PasswordReset->edit($this->params);
  }
}
