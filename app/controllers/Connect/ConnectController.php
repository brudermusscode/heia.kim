<?php

namespace Heiakim\Controller\Connect;

use Heiakim\Controller\Controller;
use Heiakim\Model\Connect\Connect;

class ConnectController extends Controller
{

  /**
   * @return string
   */
  public function start()
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

    # Connection exists?
    if ($Connect)
      return error("<strong>You have a service connected already.</strong> Remove it, to create a new one.");

    return (new Connect)->auth($this->params);
  }

  /**
   * @return string
   */
  public function create()
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
