<?php

namespace Heiakim\Controller;

use Heiakim\Model\Authentication;
use Heiakim\Model\Image;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostComment;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Utils\Arr;
use Heiakim\Model\User;
use Heiakim\Trait\ProcessesRequests;
use Dom\Comment;

class Controller
{
  use ProcessesRequests;

  /**
   * Keys in POST or GET that will always pass.
   */
  protected static array $valid_passthrough_keys = [
    "habibi",
    "csrf_token",
  ];

  /**
   * Params to send to any inheriting Controller.
   */
  protected array|object $params = [];

  public function __construct(array $params = [], array $files =  [])
  {

    # Set params.
    $this->params = $params;

    # Append files to params.
    if ($files) {
      $this->params["files"] = [];

      foreach ($files as $key => $file)
        $this->params["files"][$key] = $file;
    }
  }

  /**
   * Checks, if a current user is or is not authenticated, based
   * on the logged param. Will check if the user is logged in by default.
   *
   * @param bool $logged - Whether the user can be logged in or not.
   * @param array $can
   *        Expects an array with one key value pair which key should
   *        represent the section and which value the resource that
   *        a user should be granted for to interact with.
   * @param bool $return_json_string
   * @param bool $die_on_error
   * @return string|object
   */
  protected function authorize(
    bool $logged = true,
    array $can = [],
    User|SquadUser|null $resource = null,
    bool $respect_social_exclusion = false,
    bool $return_json_string = true,
    bool $die_on_error = true
  ) {
    return authorize(
      resource: $resource ?? CurrentUser,
      logged: $logged,
      can: $can,
      respect_social_exclusion: $respect_social_exclusion,
      return_json_string: $return_json_string,
      die_on_error: $die_on_error,
    );
  }

  /**
   * Authorize a resource is touchable by a another given
   * resource. By now, it will just accept a SquadUser as the
   * first resource.
   *
   * @param SquadUser|null $resource
   * @param Image|Comment|SquadPost|SquadPostComment|SquadFeedItem|null $item
   * @param bool $die_on_error
   * @return string|void
   */
  protected function can_interact(
    SquadUser|null $resource = null,
    Image|Comment|SquadPost|SquadPostComment|SquadFeedItem|null $item = null,
    bool $die_on_error = true
  ) {

    /**
     * @var bool
     */
    $resource_exists = $resource?->exists ?? false;

    /**
     * Resource one exists?
     */
    if (!$resource_exists)
      return request_error("!NO_PERMISSIONS", die: $die_on_error);

    /**
     * @var bool
     */
    $item_exists = $item?->exists ?? false;

    /**
     * Second resource exists?
     */
    if (!$item_exists)
      return request_error("<strong>This resource doesn't exist anymore.</strong> It might have been deleted.", die: $die_on_error);

    /**
     * Resource1 can touch the resource2? Checking for method
     * existence is obsolete since I will just accept SquadUsers
     * as the resource but this might be extended in the future.
     */
    if (method_exists($resource, "authorize_content_touch"))
      return $resource->authorize_content_touch($item, $die_on_error);

    return true;
  }

  /**
   * This bad boy is part of the authentication dialogue
   * system which requires the logged in user to authenticate with
   * a code sent to their email before firing of critical
   * functions related to their account. It takes the code sent
   * with the params automatically. User always has to be logged in.
   *
   * @param bool $die_on_error
   * @return null|string|object
   */
  public function authenticate(bool $die_on_error = true)
  {

    /**
     * @var object|string
     */
    $error = $this->error("!AUTH_FAILED");

    /**
     * Any necessary parameter for validating authentication is missing?
     */
    if (
      empty($this->params["authentication_type"])
      || empty($this->params["authentication_code"])
      || empty($this->params["authentication_token"])
    )
      return $die_on_error ? die($error) : $error;


    /**
     * @var ?Authentication
     */
    $Authentication =
      CurrentUser
      ->authentications()
      ->where("type", $this->params["authentication_type"])
      ->where("code", $this->params["authentication_code"])
      ->where("token", $this->params["authentication_token"])
      ->whereNull("deleted_at")
      ->first();

    /**
     * Authentication is existing?
     */
    if (!$Authentication)
      return $die_on_error ? die($error) : $error;

    /**
     * In case there is a value for authentication set, manipulate
     * the params array so that the actual value's type name is
     * the key instead of the authenticationn_value.
     */
    // if (get("var"))

    /**
     * Unset all parameter important for this authentication to
     * allow strict param validation later in the controller.
     */
    unset(
      $this->params["authentication_type"],
      $this->params["authentication_code"],
      $this->params["authentication_token"],
    );

    # Invalidate authentication & return.
    return $Authentication->delete();
  }

  /**
   * Validates given params having keys specified and sets the
   * result to the protected params object to make it available in
   * the scope of this classes and those inheriting it.
   *
   * @param array $strict - Strictly necessary parameter.
   * @param array $optional - Will pass, but not necessary.
   * @param ?array $input_params
   * @return void
   *
   * NOTE: Will die on error.
   */
  public function validate_params(
    array $strict,
    array $optional = [],
    ?array $input_params = null,
  ) {

    /**
     * @var ?object
     */
    $this->params = $this->serialize_request_params(
      $strict,
      $input_params ?? $this->params,
      $optional
    );

    if (!$this->params)
      die(error());
  }

  /**
   * Checks for given array keys being present in another array
   * and for array keys that are not allowed to be passed.
   *
   * @param array $necessary The keys needing to be present
   * @param array $post_params The array to check against
   * @param array $optional Let keys pass that are there but not filled
   * @return object|false
   */
  protected function serialize_request_params(
    array $necessary,
    array $post_params,
    array $optional = []
  ) {

    $always_pass = self::$valid_passthrough_keys;

    # Check if all required parameters are set in the request.
    foreach ($necessary as $param)
      if (!isset($post_params[$param]))
        return false;

    # Check if any parameter in the post request is not in the required or optional arrays
    foreach ($post_params as $key => $value)
      if (
        !in_array($key, $necessary) &&
        !in_array($key, $optional) &&
        !in_array($key, $always_pass)
      )
        return false;

    $final = (object) Arr::sanitize_special_chars($post_params, skip_keys: []);

    return $final;
  }

  /**
   * Get the magic happening! Wizards from Waverly Place have been
   * working on this: This function calls a controller file from a
   * given file inside a given path and determines the method to
   * call based on the file name this function is being called in.
   *
   * WOW.
   *
   * @param $file __FILE__
   * @param $from __DIR__
   * @return string Basic JSON return string
   */
  public static function call(string $file, string $from)
  {

    $dir_split = explode("/", $from);

    # Remove all directories before (and including) templates so we
    # can determine, how deep the Controller file lays.
    foreach ($dir_split as $key => $dir) {
      unset($dir_split[$key]);
      if ($dir === "templates") {
        break;
      }
    }

    # Build the controller name.
    $ControllerName = "Heiakim\\Controller\\";

    foreach ($dir_split as $dir) {
      $dir_split2 = explode("-", $dir);
      foreach ($dir_split2 as $dirnamepart)
        $ControllerName .= ucfirst($dirnamepart);
    }

    $ControllerName .= "sController";

    // Controller class is non-existent?
    if (!class_exists($ControllerName))
      return error("Klasse gibts nicht Bruder.");

    # Get the method name from file name.
    $method = pathinfo($file, PATHINFO_FILENAME);

    // Method is non-existent inside controller class?
    if (!method_exists($ControllerName, $method)) {
      return error("Methode gibts nicht Bruder.");
    }

    return new $ControllerName($_POST, $_FILES)->$method();
  }
}
