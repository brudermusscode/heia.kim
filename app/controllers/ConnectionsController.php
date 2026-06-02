<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Http\Request;
use Heiakim\Model\Authentication;
use Heiakim\Model\ConnectionOsu;
use Heiakim\Trait\IsProviderConnection;
use Heiakim\Utils\Utils;

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
      strict: ["provider"],
      optional: [],
    );

    # CurrentUser has a connection of this provider already?
    if (
      CurrentUser?->connections()
      ->where("provider", $this->params->provider)
      ->first()
    )
      return error("!API_CONNECTED_ALREADY");

    $ProviderClass = IsProviderConnection::ProviderClassOrDie($this->params->provider);

    return success(data: ["link" => $ProviderClass::generate_link()]);
  }

  /**
   * Creates a new instance of an existing class of a given provider by utili-
   * zing the received code from authentication screen of the third party.
   *
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["provider", "code", "state"],
      optional: ["scope"],
    );

    # CurrentUser has a provider of this type connected already?
    if (
      CurrentUser?->connections()
      ->where("provider", $this->params->provider)
      ->first()
    )
      return error("!API_CONNECTED_ALREADY");

    $ProviderClass = IsProviderConnection::ProviderClassOrDie($this->params->provider);

    /**
     * @var ConnectionOsu
     */
    $Connection = new $ProviderClass()->new($this->params);

    # Associate an existing CurrentUser with the ProviderClass.
    if (CurrentUser->exists)
      $Connection->associate(CurrentUser);

    # Create a new Authentication so the ProviderUser can create a real
    # User in the next step.
    $Authentication = Authentication::create([
      "email" => $Connection->email,
      "user_id" => $Connection->user_id,
      "type" => "user:create",
      "token" => Utils::random_alpha_token(24),
      "code" => Utils::random_numeric_token(4),
      "remote_address" => Request::get_remote_address(),
    ]);

    # Prepare the redirect URL.
    $redirect = "/begin/" . $Authentication->token . (
      # Append the username.
      $Connection->provider_user_nickname ? "?name=" . $Connection->provider_user_nickname : ""
    );

    return success(data: [
      "Connection" => $Connection,
      "redirect" => $redirect,
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
