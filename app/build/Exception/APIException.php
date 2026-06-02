<?php

namespace Heiakim\Exception;

use Exception;
use Heiakim\Application\Logger;

class APIException extends Exception
{
  public function __construct($message = "API Error", $code = 0, ?Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
    Logger::to_file($this, "api_errors.log");
  }
}
