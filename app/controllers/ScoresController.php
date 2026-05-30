<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Application\Feature;
use Bruder\Controller;
use Bruder\Http\Request;

class ScoresController extends Controller
{
  /**
   * CREATE
   *
   * @param object $params
   * @return null
   */
  public function create(array $params)
  {
    return null;
  }

  /**
   * UPDATE
   *
   * @param object $params
   * @return null
   */
  public function edit(array $params)
  {
    return null;
  }

  /**
   * DELETE
   *
   * @param object $params
   * @return null
   */
  public function remove(array $params)
  {
    return null;
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  private function sanitize_request(array $params)
  {
    return $this->serialize_request_params([], $params, ["wipe"]);
  }
}
