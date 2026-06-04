<?php

namespace Heiakim\Exception;

use Exception;
use Heiakim\Application\Logger;

class DatabaseException extends Exception
{
  public function __construct($message = "Database Error", $code = 0, ?Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
    Logger::to_file($this, "database_errors.log");
  }
}
