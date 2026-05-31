<?php

namespace Heiakim\Exception;

use Exception;
use Heiakim\Application\Logger;

class AuthorizationException extends Exception
{
  public function __construct($message = "Authorization Error", $code = 0, ?Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
    Logger::to_file($this, "auth_errors.log");
  }
}
