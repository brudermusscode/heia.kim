<?php

namespace Bruder\Heiakim\Controller\Connect;

use Bruder\Controller;
use Bruder\Heiakim\Model\Connect\ConnectDiscord;
use Bruder\Heiakim\Model\Vendor\Discord;

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
    if ($this->CurrentUser && $this->CurrentUser->discord)
      return $this->error("<strong>You have connected your account to Discord already.</strong>");

    /**
     * Append user etc.
     */
    $escaped_params->CurrentUser = $this->CurrentUser;

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
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Remove the connection.
     */
    $this->CurrentUser->discord->delete();

    return $this->success("<strong>Connection removed!</strong>");
  }

  /**
   * POST
   *
   * @param array $params
   * @return object
   */
  public function login(array $params)
  {
    $escaped_params = $this->serialize_request_params(["code", "state"], $params, []);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * User logged?
     */
    if ($this->CurrentUser)
      return $this->error("!ALREADY_LOGGED");

    /**
     * Append user etc.
     */
    $escaped_params->CurrentUser = $this->CurrentUser;
    $escaped_params->return_uri = "login";

    return (new ConnectDiscord)->login($escaped_params);
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  private function sanitize_request(array $params)
  {
    return $this->serialize_request_params([], $params, []);
  }
}
