<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Http\Request;
use Heiakim\Model\Authentication;
use Heiakim\Model\User;
use Heiakim\Utils\Utils;

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

    $this->authorize();

    # For new User: logged in already?
    if ($this->params->type === "user:create" && CurrentUser->exists)
      return error("!ALREADY_LOGGED");

    # For new User: E-Mail is not set?
    if ($this->params->type === "user:create" && empty($this->params->email))
      return error("<strong>Bro, where mail?</strong>");

    # For new User: E-Mail is not set?
    if ($this->params->type === "user:update:email" && empty($this->params->email))
      return error("<strong>Bro, where mail?</strong>");

    # Any other case: User is not logged in?
    if ($this->params->type !== "user:create" && !CurrentUser->exists)
      return error("!NOT_LOGGED");

    $token = Utils::random_alpha_token(32);
    $code = Utils::random_numeric_token(6);

    # Type is set?
    if (
      !$this->params->type || !in_array($this->params->type, Authentication::$types)
    )
      return request_error("<strong>What happened kurwa?</strong> 😂");

    # New user creation is handled differently.
    if ($this->params->type === "user:create")
      return new Authentication()->create_user($this->params);

    # If the value is set but there is nothing in it, return an error.
    if (isset($params->value) && !trim($params->value))
      return request_error(match ($params->type) {
        Authentication::$types[2] => "<strong>Put a valid mail bro</strong> 🤪",
        default => "<strong>Nothing to authenticate!</strong> Put something in! 🤭"
      });

    /**
     * Creating a user by now uses a different procedure, because
     * no user requires to be logged in.
     * @var ?Authentication
     */
    $Authentication = CurrentUser->authentications()
      ->where("type", $this->params->type)
      ->whereNull("deleted_at")
      ->first();

    $Authentication
      ? $Authentication->update([
        "email" => CurrentUser->email,
        "type" => $this->params->type,
        "token" => $token,
        "code" => $code,
        "value" => $params->value ?? null,
        "remote_address" => Request::get_remote_address(),
      ])
      : $Authentication = CurrentUser->authentications()
      ->create([
        "email" => CurrentUser->email,
        "type" => $this->params->type,
        "token" => $token,
        "code" => $code,
        "value" => $params->value ?? null,
        "remote_address" => Request::get_remote_address(),
      ]);

    # Send a mail and return.
    return !$Authentication->send_mail(
      $code,
      CurrentUser->email,
      CurrentUser->name,
    )
      ? error("<strong>We had trouble sending a mail.</strong> Try again!")
      : success("<strong>A code has been sent to your e-mail address.</strong> Be sure to check your spam folder, too!");
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

  public function delete()
  {

    $this->validate_params(
      strict: ["token"],
    );

    /**
     * @var ?Authentication
     */
    $Authentication = Authentication::where("token", $this->params->token)
      ->first();

    # Authentication does not exist?
    if (!$Authentication)
      return error();

    # Delete all relations + the Authentication.
    $Authentication->connection()->delete();
    $Authentication->delete();

    return success();
  }
}
