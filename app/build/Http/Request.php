<?php

namespace Heiakim\Http;

use Heiakim\Application\Application;

class Request
{

  /**
   * @var object
   */
  protected static $return;

  public $message;
  public $error;
  public $status;
  public $data;
  private $dd;

  public function __construct()
  {
    $this->dd = Application::get_default_dialogues();
    $this->message = "Bruder, was ist passiert?";
    $this->status = !true;
    $this->data = null;
    $this->error = $this->message;
  }

  /**
   * Sets the given message or a fallback default as the error and
   * returns itself.
   *
   * @param string $message
   * @param mixed $data
   * @param bool $json_encoded
   * @return string|object
   */
  public function error(?string $message = null, mixed $data = null, bool $return_json_string = true)
  {
    /**
     * Bang messages refer to default dialogues.
     * @var bool
     */
    $is_default_dialogue = $message && $message[0] === "!";

    /**
     * @var string
     */
    $message = $is_default_dialogue
      ? (
        $this->dd[str_replace("!", "", $message)] ?? $this->dd["INVALID_REQUEST"]
      )
      : (
        $message ?? $this->dd["INVALID_REQUEST"]
      );

    $this->data = $data;
    $this->message = $message;
    $this->error = $message;

    return $return_json_string ? json_encode($this) : $this;
  }

  /**
   * Sets the message to the given one, removes the error and sets
   * the status to true. Returns itself.
   *
   * @param ?string $message
   * @param ?mixed $data
   * @param string $message
   * @return string|object
   */
  public function success(?string $message = null, mixed $data = null, bool $return_json_string = true)
  {
    /**
     * Bang messages refer to default dialogues.
     * @var bool
     */
    $is_default_dialogue = $message && $message[0] === "!";

    /**
     * @var string
     */
    $message = $is_default_dialogue
      ? (
        $this->dd[str_replace("!", "", $message)] ?? "Woah!"
      )
      : (
        $message ?? "Woah!"
      );

    $this->message = $message;
    $this->data = $data;
    $this->error = null;
    $this->status = true;

    return $return_json_string ? json_encode($this) : $this;
  }

  /**
   * Checks if request is of type POST
   *
   * @return bool
   */
  public static function is_post()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') return true;

    return false;
  }

  /**
   * Checks if request is of type GET
   *
   * @return bool
   */
  public static function is_get()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') return true;

    return false;
  }

  /**
   * Retrieves and escapes a query parameter from the URL.
   *
   * This function checks if the specified query parameter exists
   * in the URL's query string.
   * If the parameter is found, its value is retrieved and
   * properly escaped using htmlspecialchars()
   * to prevent potential cross-site scripting (XSS) attacks.
   *
   * @param string $name The name of the query parameter to
   *    retrieve and escape.
   * @param string|null $default The default value to return if
   *    the parameter is not found. Default: null.
   * @return string|null The escaped value of the query parameter,
   *    or null if the parameter is not found.
   */
  public static function sanitize_get_param(string $name, mixed $default = null)
  {
    if (isset($_GET[$name]))
      return htmlspecialchars($_GET[$name]);

    return $default;
  }

  /**
   * Return real estate client IP.
   *
   * @return ?string
   */
  public static function get_remote_address()
  {

    $candidates = [];

    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']))
      $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];

    if (!empty($_SERVER['HTTP_X_REAL_IP']))
      $candidates[] = $_SERVER['HTTP_X_REAL_IP'];

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
      foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $ip)
        $candidates[] = trim($ip);

    if (!empty($_SERVER['REMOTE_ADDR']))
      $candidates[] = $_SERVER['REMOTE_ADDR'];

    foreach ($candidates as $ip)
      if (filter_var($ip, FILTER_VALIDATE_IP))
        return $ip;

    return null;
  }

  /**
   * Validates whether a given string contains only integers
   * separated by commas.

   *
   * Examples:
   * - "1,2,3,4,5" - valid
   * - "123" - valid
   * - "1, 2,3" - invalid
   * - "1,,3" - invalid
   * - "1,2,a,4" - invalid
   *
   * @param string $input The string to validate.
   * @return bool True if the string is valid, false otherwise.
   */
  public static function get_is_array_of_ints(string $input)
  {
    return preg_match('/^\d+(,\d+)*$/', $input) === 1;
  }
}
