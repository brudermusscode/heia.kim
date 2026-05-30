<?php

namespace Bruder;

use Bruder\Heiakim\Model\Authentication;
use Bruder\Heiakim\Model\Image;
use Bruder\Heiakim\Model\Squad\SquadFeedItem;
use Bruder\Heiakim\Model\Squad\SquadPost;
use Bruder\Heiakim\Model\Squad\SquadPostComment;
use Bruder\Heiakim\Model\Squad\SquadUser;
use Bruder\Utils\Arr;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Trait\ProcessesRequests;
use Bruder\Http\Request;
use Dom\Comment;

class Controller
{
  use ProcessesRequests;

  /**
   * Keys that are valid for prequests even tho not explicitly
   * noted down in the controller
   *
   * @var array
   */
  protected $valid_passthrough_keys = [
    "habibi",
    "csrf_token",
  ];

  /**
   * @var array|object
   */
  protected $params = [];

  /**
   * @var ?User
   */
  protected $CurrentUser;

  public function __construct(array $params = [])
  {
    /**
     * Set current user.
     */
    $this->CurrentUser = $this->get_current_user();

    /**
     * Set the input parameter.
     */
    $this->params = $params;
  }

  /**
   * @return ?User
   */
  protected function get_current_user()
  {
    /**
     * @var int
     */
    $session_id = $_SESSION["session"]->user_id ?? 0;

    /**
     * @var User
     */
    $User = User::find($session_id) ?? User::guest();

    if ($User && $User->exists && $User->privacy->accepts_policies)
      return $User;

    return null;
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
   * @param bool $return_json_string - Whether to return a
   *        json_encoded Request result or the object itself.
   * @param bool $die_on_error
   *        Whether it should exit all further execution on error or just
   *        return the request object
   * @return die|string|object
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
      resource: $resource ?? $this->CurrentUser,
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
   * @param Image|Comment|SquadPost|SquadPostComment|SquadFeedItem|null $resource2
   * @param bool $die_on_error
   * @return string|die|void
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
    $Authentication = $this->CurrentUser
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

    /**
     * Invalidate authentication & return.
     */
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
   * @param bool $return_json_string
   * @param bool $die_on_error
   * @return string|object
   */
  public function validate_params(array $strict, array $optional = [], ?array $input_params = null, bool $return_json_string = true, bool $die_on_error = true)
  {
    /**
     * @var ?object
     */
    $this->params = $this->serialize_request_params($strict, $input_params ?? $this->params, $optional);

    return !$this->params
      ? request_error(return_json_string: $return_json_string, die: $die_on_error)
      : request_success(return_json_string: $return_json_string);
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
  protected function serialize_request_params(array $necessary, array $post_params, array $optional = [])
  {
    /**
     * @var array
     */
    $always_pass = [
      "csrf_token",
      "habibi",
    ];

    // Check if all required parameters are set in the post request
    foreach ($necessary as $param)
      if (!isset($post_params[$param]))
        return false;

    // Check if any parameter in the post request is not in the required or optional arrays
    foreach ($post_params as $key => $value)
      if (!in_array($key, $necessary) && !in_array($key, $optional) && !in_array($key, $always_pass))
        return false;

    /**
     * @var array
     */
    $serializedParams = [];

    /**
     * Sanitize all values recursively.
     */
    foreach ($post_params as $key => $value)
      if (is_array($value))
        $serializedParams[$key] = Arr::sanitize_special_chars($value);
      else
        $serializedParams[$key] = is_numeric($value) ? (int) $value : filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);

    /**
     * Append the current user to make it available in any controller.
     */
    $serializedParams["CurrentUser"] = $this->CurrentUser;

    return (object) $serializedParams;
  }

  /**
   * Unset any key from an array except the given ones.
   *
   * @param string ...$keys The keys to retain in the array.
   * @param array[string] $array The original array.
   * @return ?object
   */
  function restrict_params(array $restrict, array $array): ?object
  {
    foreach ($array as $key => $arr) {
      if (!array_key_exists($key, array_flip($restrict))) unset($array[$key]);
    }

    return $array ? (object) $array : null;
  }
}
