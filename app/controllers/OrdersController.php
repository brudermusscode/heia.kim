<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Http\Request;
use Bruder\Controller;
use Bruder\Heiakim\Model\Order;

class OrdersController extends Controller
{

  /**
   * POST
   *
   * @return object
   */
  public function create(array $params)
  {
    return Request::append_error($this->return, "No");
  }

  /**
   * DELETE
   *
   * @return object
   */
  public function remove(array $params)
  {
    return Request::append_error($this->return, "No");
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
