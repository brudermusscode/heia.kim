<?php

use Heiakim\Exception\ApiException;
use Heiakim\Http\Request;
use Heiakim\Model\User;
use Heiakim\Model\Squad\SquadUser;

function _root()
{
  return dirname($_SERVER["DOCUMENT_ROOT"]) ?: "/data";
}

define("ROOT", _root());
define("LOG_EOL", "\n\n-----------------------------------\n\n");

/**
 * Gets oauth credentials for a specified key from /config/oauth.php.
 *
 * @return array
 * @throws ApiException
 */
function oauth_credentials(string $key)
{
  $arr = require ROOT . "/config/security/oauth.php";

  return $arr[$key] ?? null;
}

/**
 * @param ?string $message
 * @param ?mixed $data
 * @return object|string
 */
function success(?string $message = null, mixed $data = null, bool $json_encoded = true)
{
  return (new Request)->success($message, $data, $json_encoded);
}

/**
 * @param ?string $message
 * @param ?mixed $data
 * @return object|string
 */
function error(?string $message = null, mixed $data = null, bool $json_encoded = true)
{
  return (new Request)->error($message, $data, $json_encoded);
}

/**
 * Get a specific value for a key from the .env file.
 *
 * @param string $key
 * @return ?mixed
 * @throws Exception
 */
function _env(?string $key = null)
{

  $env_file_path = ROOT . "/.env";

  /**
   * Environment variables file is missing?
   */
  if (!file_exists($env_file_path))
    throw new Exception("❌ Environment variables file not found. Create a file named '.env' in the project root directory and add variables according to what is needed for this specific project. Usually, there is a .env.example file somewhere to copy from.");

  /**
   * @var object
   */
  $parsed = (object) parse_ini_file($env_file_path);

  return !$key ? $parsed : ($parsed->$key ?? null);
}

/**
 * @return string
 */
function current_env()
{
  return _env("ENVIRONMENT");
}

/**
 * Sanitizes the complete given html from whitespace and comments.
 *
 * @param string $html
 * @return string
 */
function sanitize_output($html)
{
  $search = array(
    '/(\n|^)(\x20+|\t)/',
    '/(\n|^)\/\/(.*?)(\n|$)/',
    '/\n/',
    '/\<\!--.*?-->/',
    '/(\x20+|\t)/', # Delete multispace (Without \n)
    '/\>\s+\</', # strip whitespaces between tags
    '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
    '/=\s+(\"|\')/'
  ); # strip whitespaces between = "'

  $replace = array(
    "\n",
    "\n",
    " ",
    "",
    " ",
    "><",
    "$1>",
    "=$1"
  );

  $html = preg_replace($search, $replace, $html);
  return $html;
}

/**
 * Print out anything in nicley formatted pattern.
 *
 * @param mixed
 * @return void
 */
function pdie(mixed ...$a)
{
  /**
   * Bad request header, so the frontend will include the
   * exception-container on any request method.
   */
  header("HTTP/1.0 400 Bad request");

  echo <<<HTML
  <exception-container>
    <mbutton wide close-overlay icon-only clickable
      style="position:fixed;top:1.2em;right:1.2em;">
      <mi wide>emoji_symbols</mi>
    </mbutton>
    <div p42 style="overflow:auto;max-height:100vh;">
      <pre force-word-wrap>
  HTML;

  var_dump($a);

  echo <<<HTML
      </pre>
    </div>
  </exception-container>
  HTML;

  die;
}

/**
 * Just return OK!
 */
function OK()
{
  pdie("OK!");
}

/**
 * @param string $key
 * @return ?mixed
 */
function GET(?string $key = null)
{
  return $key ? (GET->$key ?? null) : GET;
}

/**
 * @param string $message
 * @param mixed $data
 * @param bool $return_json_string
 * @param bool $die
 * @return string|object
 *
 * NOTE: Might die on error.
 */
function request_error(?string $message = null, mixed $data = null, bool $return_json_string = true, bool $die = false)
{
  $Request = (new Request)->error($message, $data, $return_json_string);

  return $die && $return_json_string ? die($Request) : $Request;
}

/**
 * @param User $Resource
 * @return void
 *
 * NOTE: Redirects through header() on error.
 */
function redirect_unauthorized(User $Resource = CurrentUser)
{
  if (!$Resource->exists) {
    header("location: /not-found");
    exit;
  }
}

/**
 * @param string $message
 * @param mixed $data
 * @param bool $return_json_string
 * @param bool $die
 * @return string|object
 *
 * NOTE: Might die on error.
 */
function request_success(?string $message = null, mixed $data = null, bool $return_json_string = true, bool $die = false)
{
  $Request = (new Request)->success($message, $data, $return_json_string);

  return $die && $return_json_string ? die($Request) : $Request;
}



/**
 * Validates authorization of a given resource to take action on
 * another resource. Includes optional checks for social exclusion
 * which restricts the interaction of the resource with the community.
 *
 * @param null|User|SquadUser $resource
 * @param bool $logged
 * @param array $can
 * @param bool $respect_social_exclusion
 * @param bool $return_json_string
 * @param bool $die_on_error
 * @return string|object
 *
 * NOTE: can[] will only fire if the given array has exactly 2 keys.
 * NOTE: Might die on error.
 */
function authorize(
  null|User|SquadUser $resource,
  bool $logged = true,
  array $can = [],
  bool $respect_social_exclusion = false,
  bool $return_json_string = true,
  bool $die_on_error = true
) {

  /**
   * @var bool
   */
  $resource_exists = $resource?->exists ?? false;

  /**
   * There is no resource or it doesn't exist yet + the user
   * doesn't have to be logged in - We return!
   */
  if (!$resource_exists && !$logged)
    return request_success(return_json_string: $return_json_string);

  /**
   * No resource exists but it has to be logged in?
   */
  if (!$resource_exists && $logged)
    return request_error("!NO_PERMISSIONS", return_json_string: $return_json_string, die: $die_on_error);

  /**
   * The resource is logged in but can't be?
   */
  if ($resource_exists && !$logged)
    return request_error("!ALREADY_LOGGED", return_json_string: $return_json_string, die: $die_on_error);

  /**
   * Respecting social exclusion, is the user excluded from
   * community functionality?
   */
  if ($respect_social_exclusion && $resource_exists && $resource->is_socially_excluded())
    return request_error("!SOCIALLY_EXCLUDED", return_json_string: $return_json_string, die: $die_on_error);

  /**
   * A can is set but no resource exists or the resource has not
   * enough permissions?
   */
  if (count($can) === 2 && (!$resource_exists || !$resource?->can($can[0], $can[1])))
    return request_error("!NO_PERMISSIONS", return_json_string: $return_json_string, die: $die_on_error);

  return request_success(return_json_string: $return_json_string);
}

/**
 * Redirects to another location and dies.
 *
 * @param string $uri
 * @return void
 *
 * NOTE: Will die on error.
 */
function redirect(string $uri, array $headers = [])
{
  /**
   * Set given headers.
   */
  foreach ($headers as $header) {
    header($header);
  }

  /**
   * Set header location.
   */
  header("location: $uri");

  die;
}

/**
 * Includes template files. It gets rid for the need of
 * underscores and .php endings.
 *
 * @param string $file
 * @param bool $is_partial
 * @return void
 *
 * NOTE: Includes a found template.
 */
function template(string $file, bool $is_partial = true, array $variables = [])
{
  $has_php_ending = strrpos($file, ".php");
  $slash_position = strrpos($file, "/");

  /**
   * If the file path has a / and its not a partial, replace the /
   * with /_ as all partials should have the convention of being
   * named with underscores at the beginning.
   */
  if ($slash_position !== FALSE && $is_partial) {
    $file_path = substr_replace($file, "/_", $slash_position, 1);

    /**
     * If the file path has no / but still is a partial, just
     * add a _ at the beginning.
     */
  } else if (!$slash_position && $is_partial) {
    $file_path = "_$file";

    /**
     * Just use the file path if none of the above match.
     */
  } else {
    $file_path = $file;
  }

  /**
   * Extracts variables to make them available in this functions
   * scope from outside.
   */
  extract($variables);

  include ROOT . "/app/templates/$file_path" . (!$has_php_ending ? ".php" : "");
}

/**
 * Includes helper partial files. It gets rid for the need of
 * underscores and .php endings.
 *
 * @param string $file
 * @param bool $is_partial
 * @param array $variables
 * @return void
 *
 * NOTE: Includes a found template.
 */
function helper(string $file, bool $is_partial = true, array $variables = [])
{
  $has_php_ending = strrpos($file, ".php");
  $slash_position = strrpos($file, "/");

  if ($slash_position !== FALSE) {
    $file_path = substr_replace($file, "/_", $slash_position, 1);
  } else if ($is_partial) {
    $file_path = "_$file";
  }

  /**
   * Extracts variables to make them available in this functions
   * scope from outside.
   */
  extract($variables);

  include HELPER . "/$file_path" . ($is_partial && !$has_php_ending ? ".php" : "");
}

/**
 * @param string $path
 * @return void
 */
function image(string $path)
{
  echo IMAGE . "/$path";
}

/**
 * @param mixed $parameter
 * @param string|array $case
 * @return string
 */
function display_active(mixed $parameter, mixed $case = null)
{
  if ($case === null)
    echo $parameter === null ? "active" : "";
  else if ($case === "")
    echo $parameter === "" ? "active" : "";
  else
    echo is_array($case)
      ? (
        in_array($parameter, $case)
        ? "active"
        : ""
      )
      : ($parameter === $case ? "active" : "");
}

/**
 * @param mixed $condition
 * @return void|string
 */
function display_active_when_condition($condition)
{
  echo $condition ? "active" : "";
}

/**
 * @param bool $condition
 * @param ?string $custom_attribute
 * @return void
 */
function display_disabled_if(bool $condition, ?string $custom_attribute = null)
{
  echo $condition ? $custom_attribute ?? "disabled" : "";
}
