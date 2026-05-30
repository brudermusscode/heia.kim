<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\Session;
use Bruder\Application\Feature;

class SessionsController extends Controller
{
  /**
   * POST
   *
   * @return object
   */
  public function create()
  {

    /**
     * Feature disabled?
     */
    if (!Feature::is_enabled("login"))
      return $this->error("!FEATURE_DISABLED");


    $this->validate_params(
      strict: ["login", "password"],
      optional: [],
    );

    /**
     * User already logged in?
     */
    $this->authorize(logged: false);

    return (new Session)->new($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["token"],
      optional: [],
    );

    /**
     * User is not logged in?
     */
    $this->authorize();

    /**
     * @var ?Session
     */
    $Session = $this->CurrentUser
      ->sessions()
      ->where("token", $this->params->token)
      ->first();

    /**
     * Session exists?
     */
    if (!$Session)
      return $this->error();

    /**
     * Already deleted?
     */
    if ($Session->deleted_at)
      return $this->error();

    return $Session->remove($this->params);
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  public function serialize_params(array $params)
  {
    return $this->serialize_request_params(["login", "password"], $params, ["api", "action", "code", "state"]);
  }
}
