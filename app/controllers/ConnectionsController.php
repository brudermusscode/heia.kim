<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Http\Request;
use Heiakim\Utils\Utils;
use Heiakim\Registry\ApiConnectionRegistry;
use Heiakim\Model\Authentication;
use Heiakim\Model\ConnectionDiscord;
use Heiakim\Model\ConnectionGithub;
use Heiakim\Model\ConnectionOsu;
use Heiakim\Model\Session;
use Heiakim\Model\User;

class ConnectionsController extends Controller
{

  /**
   * This functions creates a new OAuth link to a given Vendor's API. Nothing
   * more. It starts the authentication so to speak. User's shall be able to
   * create new calls while logged in or logged out.
   *
   * @return string
   */
  public function start()
  {

    $this->validate_params(
      strict: ["provider", "action"],
      optional: [],
    );

    # CurrentUser has a connection of this provider already?
    if (
      CurrentUser->connections()
      ->where("provider", $this->params->provider)
      ->first()
    )
      return error("!API_CONNECTED_ALREADY");

    $ProviderClass = ApiConnectionRegistry::ClassOrDie($this->params->provider);

    return success(data: [
      "link" => $ProviderClass->generate_link(action: $this->params->action)
    ]);
  }

  /**
   * Creates a new instance of an existing class of a given provider by utili-
   * zing the received code from authentication screen of the third party.
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["provider", "code", "state"],
    );

    # CurrentUser has a provider of this type connected already?
    if (
      CurrentUser->connections()
      ->where("provider", $this->params->provider)
      ->first()
    )
      return error("!API_CONNECTED_ALREADY");

    $ProviderClass = ApiConnectionRegistry::ClassOrDie($this->params->provider);

    /**
     * @var ConnectionOsu|ConnectionDiscord|ConnectionGithub
     */
    $Connection = $ProviderClass->new($this->params);

    # Associate an existing CurrentUser with the ProviderClass and return.
    if (CurrentUser->exists) {
      $Connection->user()
        ->associate(CurrentUser)
        ->save();

      return success($Connection->provider . " connected!");
    }

    # ? ---------------------------------------
    # ? From here only when a new user signs up.

    # Set a variable to tell the frontend, whether the username from the vendor's user
    # is in use in our app already.
    $name_in_use =
      User::where("name", $Connection->provider_user_nickname)
      ->orWhere("safe_name", $Connection->provider_user_nickname)
      ->exists();

    # Create a new Authentication so the ProviderUser can create a real User in the
    # next step.
    $Authentication = Authentication::create([
      "email" => $Connection->email,
      "user_id" => $Connection->user_id,
      "type" => "user:create",
      "token" => Utils::random_alpha_token(24),
      "code" => Utils::random_numeric_token(4),
      "remote_address" => Request::get_remote_address(),
    ]);

    # Make the Authentication relate to the Connection 🙂
    $Connection->authentication()
      ->associate($Authentication)
      ->save();

    # Prepare the redirect URL.
    $redirect = "/begin/" . $Authentication->token . (
      # Append the username.
      $Connection->provider_user_nickname
      ? "?name=" . $Connection->provider_user_nickname
      : "?cool=1"
      # Append name in use.
    ) . ($name_in_use ? "&name_in_use=1" : "");

    return success(data: [
      "Connection" => $Connection,
      "redirect" => $redirect,
    ]);
  }

  /**
   * Handles the login action.
   *
   * @return string
   */
  public function reconnect()
  {

    $this->validate_params(
      strict: ["provider", "code", "state"],
    );

    # CurrentUser has a provider of this type connected already?
    if (
      CurrentUser->connections()
      ->where("provider", $this->params->provider)
      ->first()
    )
      return error("!API_CONNECTED_ALREADY");

    $ProviderClass = ApiConnectionRegistry::ClassOrDie($this->params->provider);

    /**
     * @var ConnectionOsu|ConnectionDiscord|ConnectionGithub
     */
    $Connection = $ProviderClass->reconnect($this->params);

    # Create a new Session!
    new Session()->new($Connection->user);

    return success(data: [
      "redirect" => "/home",
    ]);
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["provider"],
      optional: [],
    );
  }
}
