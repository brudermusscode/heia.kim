<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Connection;
use Heiakim\Model\ConnectionDiscord;
use Heiakim\Model\ConnectionGoogle;
use Heiakim\Model\ConnectionOsu;

class ConnectionsController extends Controller
{

  /**
   * This functions creates a new OAuth link to a given Vendor's
   * API. Nothing more. It starts the authentication so to speak.
   * User's shall be able to create new calls while logged in or
   * logged out.
   *
   * @return string
   */
  public function start()
  {

    # [provider] has to match the exact class name.

    $this->validate_params(
      strict: ["provider"],
      optional: [],
    );

    if (
      CurrentUser->exists &&
      CurrentUser->connections()
      ->where("type", $this->params->type)
      ->first()
    )
      return error("!API_CONNECTED_ALREADY");

    # Vendor class doesn't exist?
    if (!($ProviderClass = Connection::map_provider($this->params->provider)))
      return error("!INVALID_API_CALL");

    /**
     * @var class-string<ConnectionDiscord|ConnectionOsu|ConnectionGoogle> $ProviderClass
     */

    return success(data: ["link" => $ProviderClass::generate_link()]);
  }

  /**
   * @return string
   */
  public function create_old()
  {

    $this->validate_params(
      strict: ["type", "code", "state"],
      optional: ["scope", "authuser", "prompt"],
    );

    /**
     * Authorize the user in a not so cool way.
     */
    if (!$this->authorize(return_json_string: false, die_on_error: false)->status)
      return request_error("!NO_PERMISSIONS", return_json_string: false);

    /**
     * @var ?Connect
     */
    $Connect = CurrentUser
      ->connections()
      ->where("type", $this->params->type)
      ->first();

    /**
     * Return an error, if a connection of that type already exists.
     */
    if ($Connect)
      return request_error("<strong>You have a service connected already.</strong> Remove it, to create a new one.", return_json_string: false);

    return (new Connect)->new($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["type"],
      optional: [],
    );

    $this->authorize();

    /**
     * @var ?Connect
     */
    $Connect = CurrentUser
      ->connections()
      ->where("type", $this->params->type)
      ->first();

    /**
     * Connection doesn't exist?
     */
    if (!$Connect)
      return request_error("<strong>You have no service connected.</strong>");

    $Connect->delete();

    return request_success("<strong>Connection deleted!</strong> You can add another one at any time 😁");
  }
}
