<?php

namespace Bruder\Heiakim\Trait;

use Bruder\Http\Request;

trait ProcessesRequests
{

  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,, REQUEST & RETURN ,,,,,,,,,,,,,,,,,,,,,, */
  /** ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, */

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
