<?php

namespace Heiakim\Application;

use Exception;

class Logger
{

  /**
   * Logs a given message to a specified file, which will be cerated if not exists.
   * Returns the Exception itself, so the function can be thrown if wanted 😃.
   *
   * @param Exception $exception
   * @param string $logFile
   * @return Exception
   */
  public static function to_file(Exception $exception, string $logFile = 'php_errors.log')
  {

    # Get the root directory path using Application::root()
    $logFilePath = _root() . "/storage/logs/" . $logFile;

    # Prepare the message with timestamp, exception details
    $formattedMessage = "[" . date('Y-m-d H:i:s') . "] " .
      "Exception: " . get_class($exception) . PHP_EOL .
      "Message: " . $exception->getMessage() . PHP_EOL .
      "File: " . $exception->getFile() . " (Line: " . $exception->getLine() . ")" . PHP_EOL .
      "Stack Trace: " . PHP_EOL . $exception->getTraceAsString() . PHP_EOL .
      "-------------------------------" . PHP_EOL;

    # Check if log directory exists, if not create it
    if (!is_dir(dirname($logFilePath)))
      mkdir(dirname($logFilePath), 0777, true);  // Create the logs directory if not exists

    # Append the message to the log file
    file_put_contents($logFilePath, $formattedMessage, FILE_APPEND);

    return $exception;
  }

  /**
   * Logs a given message to the php_errors.log file.
   *
   * @param string $message The message to log.
   * @param string $logFile The path to the log file. Default is 'php_errors.log'.
   */
  public static function message_to_file(string $message, string $logFile = 'php_errors.log')
  {
    // Get the root directory path using Application::root()
    $logFilePath = _root() . "/storage/logs/" . $logFile;

    // Prepare the message with timestamp, exception details
    $formattedMessage = "[" . date('Y-m-d H:i:s') . "] " .
      $message . PHP_EOL .
      "………………………………………………………………" . PHP_EOL;

    // Check if log directory exists, if not create it
    if (!is_dir(dirname($logFilePath))) {
      mkdir(dirname($logFilePath), 0777, true);  // Create the logs directory if not exists
    }

    // Append the message to the log file
    file_put_contents($logFilePath, $formattedMessage, FILE_APPEND);
  }
}
