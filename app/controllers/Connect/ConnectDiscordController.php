<?php

namespace Heiakim\Controller\Connect;

use Heiakim\Controller\Controller;
use Heiakim\Model\Connect\ConnectDiscord;
use Heiakim\Model\Vendor\Discord;

class ConnectDiscordController extends Controller
{

  /**
   * @param array $params
   * @return object
   */
  public function auth(array $params)
  {
    $escaped_params = $this->serialize_request_params([], $params, ["return_uri"]);

    return (new Discord)->auth($escaped_params);
  }

  /**
   * POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params(["code", "state"], $params, []);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * If user is logged, already having a discord connection?
     */
    if (CurrentUser && CurrentUser->discord)
      return $this->error("<strong>You have connected your account to Discord already.</strong>");

    /**
     * Append user etc.
     */
    $escaped_params->CurrentUser = CurrentUser;

    return (new ConnectDiscord)->new($escaped_params);
  }

  /**
   * DELETE
   *
   * @param array $params
   * @return object
   */
  public function remove(array $params)
  {
    $escaped_params = $this->serialize_request_params([], $params, []);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * If user is logged, already having a discord connection?
     */
    if (!CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Remove the connection.
     */
    CurrentUser->discord->delete();

    return $this->success("<strong>Connection removed!</strong>");
  }

  /**
   * @param array $params
   * @return object
   */
  public function login(array $params)
  {

    $this->validate_params(
      strict: ["code", "state"],
    );

    $this->authorize();

    # Append params.
    $this->params->return_uri = "login";

    return (new ConnectDiscord)->login($this->params);
  }
}
