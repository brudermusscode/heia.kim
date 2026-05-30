<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\PasswordReset;

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

    /**
     * Append the mail to the params object if a user is logged in
     * right now.
     */
    if ($this->CurrentUser)
      $this->params["mail"] = $this->CurrentUser->email;

    $this->validate_params(
      strict: ["mail"],
      optional: ["token", "password"],
    );

    return (new PasswordReset)->new($this->params);
  }

  /**
   * PUT
   *
   * @return object
   */
  public function update()
  {

    /**
     * Append the mail to the params object if a user is logged in
     * right now.
     */
    if ($this->CurrentUser)
      $this->params->mail = $this->CurrentUser->email;

    $this->validate_params(
      strict: ["token", "password"],
      optional: ["mail"],
    );

    /**
     * @var ?PasswordReset
     */
    $Reset = PasswordReset::where("token", $this->params->token)
      ->first();

    /**
     * Reset doesn't exist?
     */
    if (!$Reset)
      return $this->error();

    return $Reset->edit($this->params);
  }
}
