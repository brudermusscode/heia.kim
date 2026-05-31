<?php

use Heiakim\Http\Request;
use Heiakim\Model\User;
use Heiakim\Model\Squad\SquadUser;

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
 * @return string
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
    <div o-closer hoverable circled style="position:fixed;top:1.2em;right:1.2em;">
      <mi wide>close</mi>
    </div>
    <div p42>
      <h1 text wider bold>pdie() returned</h1>
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
 * @return string
 */
function _root()
{
  return _root();
}

/**
 * @return string
 */
function current_env()
{
  return _env("ENVIRONMENT");
}

/**
 * Redirects to another location and dies.
 *
 * @param string $uri
 * @return die
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
 * @return require The template
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

  include _root() . "/app/templates/$file_path" . (!$has_php_ending ? ".php" : "");
}


/**
 * Includes helper partial files. It gets rid for the need of
 * underscores and .php endings.
 *
 * @param string $file
 * @param bool $is_partial
 * @param array $variables
 * @return include
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
 * Echoes active if the value matches the match.
 *
 * @param mixed
 * @param mixed
 * @return void
 */
function display_active_when(mixed $value, mixed $matches)
{
  if ($value === $matches)
    echo "active";

  return;
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
 * Validates authorization of a given resource to take action on
 * another resource.
 *
 * @param null|User|SquadUser $resource
 * @param bool $logged
 * @param array $can
 * @param bool $die_on_error
 * @return void|string
 *
 * NOTE: Might die on error.
 */
function authorize(null|User|SquadUser $resource, bool $logged = true, array $can = [], bool $die_on_error = true)
{
  $Request = new Request;

  /**
   * @var ?string
   */
  $error = null;

  /**
   * Resource is null?
   */
  if (!$resource)
    $error = "!NO_PERMISSIONS";

  /**
   * User needs to be logged in.
   */
  if ($logged && !$resource->exists)
    $error = "!NO_PERMISSIONS";

  /**
   * User can't be logged in
   */
  if (!$logged && $resource->exists)
    $error = "!NO_PERMISSIONS";

  /**
   * User has enought permissions to trigger the given action?
   */
  if (count($can) === 1 && !$resource?->can(array_key_first($can), $can[0]))
    $error = "!NO_PERMISSIONS";

  /**
   * If all passed, just return into the void.
   */
  if (!$error) return;

  return $die_on_error ? die($Request->error($error)) : $Request->error($error);
}

/**
 * @param mixed $parameter
 * @param string|array $case
 * @return void
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
 * @param bool $condition
 * @param ?string $custom_attribute
 * @return void
 */
function display_disabled_if(bool $condition, ?string $custom_attribute = null)
{
  echo $condition ? $custom_attribute ?? "disabled" : "";
}
