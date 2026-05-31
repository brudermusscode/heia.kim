<?php

namespace Heiakim\Controller\Connect;

use Heiakim\Http\Request;
use Heiakim\Controller\Controller;
use Heiakim\Model\Connect\ConnectGoogle;
use Heiakim\Model\Vendor\Google;

class ConnectGoogleController extends Controller
{

  /**
   * @param array $params
   * @return object
   */
  public function auth(array $params)
  {
    $escaped_params = $this->serialize_request_params([], $params, ["return_uri"]);

    return (new Google)->auth($escaped_params);
  }

  /**
   * POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params(["code", "state"], $params, ["scope"]);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * If user is logged, already having a google connection?
     */
    if (CurrentUser && CurrentUser->google)
      return $this->error("<strong>You have connected your account to Google already.</strong>");

    /**
     * Append user etc.
     */
    $escaped_params->CurrentUser = CurrentUser;

    return (new ConnectGoogle)->new($escaped_params);
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
    CurrentUser->google->delete();

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
    $escaped_params = $this->serialize_request_params(["code", "state"], $params, ["scope"]);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * User logged?
     */
    if (CurrentUser)
      return $this->error("!ALREADY_LOGGED");

    /**
     * Append user etc.
     */
    $escaped_params->CurrentUser = CurrentUser;
    $escaped_params->return_uri = "login";

    return (new ConnectGoogle)->login($escaped_params);
  }
}
