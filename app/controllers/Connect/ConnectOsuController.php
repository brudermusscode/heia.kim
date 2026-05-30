<?php

namespace Bruder\Heiakim\Controller\Connect;

use Bruder\Application\Application;
use Bruder\Http\Request;
use Bruder\Controller;
use Bruder\Heiakim\Model\Connect\ConnectOsu;
use Bruder\Heiakim\Model\Vendor\Osu;

class ConnectOsuController extends Controller
{

  /**
   * @param array $params
   * @return object
   */
  public function auth(array $params)
  {
    $escaped_params = $this->serialize_request_params([], $params, ["return_uri"]);

    return (new Osu)->auth($escaped_params);
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
     * If user is logged, already having a connection?
     */
    if ($this->CurrentUser && $this->CurrentUser->osu)
      return $this->error("<strong>You have connected your account already.</strong>");

    /**
     * Append user etc.
     */
    $this->params->CurrentUser = $this->CurrentUser;

    return (new ConnectOsu)->new($escaped_params);
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
    $this->CurrentUser->osu->delete();

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
    $this->params->CurrentUser = $this->CurrentUser;
    $escaped_params->return_uri = "login";

    return (new ConnectOsu)->login($escaped_params);
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
