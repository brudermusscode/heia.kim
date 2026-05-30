<?php

namespace Bruder\Heiakim\Controller\Beatmap;

use Bruder\Http\Request;
use Bruder\Controller;

class SetsController extends Controller
{
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
