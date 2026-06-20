<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Http\Request;
use Heiakim\Model\Authentication;
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

    # Every authentication has a specific type, an action to authenticate. And for ea-
    # ch action there has to be different params set. This will validate this and die
    # on error.
    Authentication::validate_params($this->params);

    if (
      !$this->params->type
      || !in_array($this->params->type, Authentication::$types)
    )
      return error("What happened? 😂");

    # If the value is set but there is nothing in it, return an error.
    if (isset($params->value) && !trim($params->value))
      return request_error(match ($params->type) {
        Authentication::$types[2] => "Put a valid mail brother 🤪",
        default => "Nothing to authenticate! 🤭"
      });

    $token = Utils::random_alpha_token(32);
    $code = Utils::random_numeric_token(6);

    /**
     * @var ?Authentication
     */
    $Authentication = CurrentUser->authentications()
      ->where("type", $this->params->type)
      ->whereNull("deleted_at")
      ->first();

    $arr = [
      "email" => CurrentUser->email,
      "type" => $this->params->type,
      "token" => $token,
      "code" => $code,
      "value" => $this->params->value ?? null,
      "remote_address" => Request::get_remote_address(),
    ];

    $Authentication
      ? $Authentication->update($arr)
      : $Authentication = CurrentUser->authentications()->create($arr);

    # Send a mail and return.
    return !$Authentication->send_mail(
      $code,
      CurrentUser->email,
      CurrentUser->name,
    )
      ? error("<strong>We had trouble sending a mail.</strong> Try again!")
      : success("<strong>A code has been sent to your e-mail address.</strong> Be sure to check your spam folder, too!", data: ["type" => $this->params->type]);
  }

  /**
   * @return string
   */
  public function update()
  {

    $this->authenticate();

    # Based on the type, return a corresponding controller-action.
    return match ($this->params->authentication_type) {
      default => error(),
      "user:update:email" => new UsersController([
        "email" => $this->params->authentication_value
      ])->update(),
    };
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
