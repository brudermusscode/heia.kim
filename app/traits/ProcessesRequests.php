<?php

/**
 * A returned Request usually is a JSON encoded string and has the
 * following pattern:
 * {
 *    status: true|false,
 *    message: "…",
 *    error: "…",
 *    data: { … },
 * }
 * This allows the frontend to work effectively when a request has
 * un/successfully executed and show a well considered and designed
 * response. Data should in most cases contain a Model relation in-
 * stance or HTML to append to the DOM. Who needs React, let's be
 * honest :D
 */

namespace Heiakim\Trait;

use Heiakim\Http\Request;

trait ProcessesRequests
{

  /**
   * @param string $message
   * @return object|string
   */
  public function error(?string $message = null, bool $return_json_string = true)
  {
    return (new Request)->error($message, return_json_string: $return_json_string);
  }

  /**
   * @param ?string $message
   * @param ?mixed $data
   * @return object|string
   */
  public function success(?string $message = null, mixed $data = null, bool $return_json_string = true)
  {
    return (new Request)->success($message, $data, return_json_string: $return_json_string);
  }
}
