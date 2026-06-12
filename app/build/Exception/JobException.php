<?php

namespace Heiakim\Exception;

use Exception;
use Heiakim\Application\Logger;

class JobException extends Exception
{
  public function __construct($message = "Error while running Job", $code = 0, ?Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
    Logger::to_file($this, "job_errors.log");
  }
}
