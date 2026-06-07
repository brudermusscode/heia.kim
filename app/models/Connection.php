<?php

/**
 * This class represents any connection to a vendor.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Trait\IsConnectionProvider;
use Heiakim\Http\CURL;
use Heiakim\Registry\ApiRegistry;
use Heiakim\Time\Time;
use Heiakim\Utils\Utils;

class Connection extends Justin
{
  use IsConnectionProvider;

  /**
   * Represents the vendor's name which is used exactly like this around
   * the whole application. Naming conventions are what make magic poss-
   * ible!
   */
  protected const string PROVIDER = "";

  /**
   * Actions to trigger like signing up or logging back in.
   */
  protected array $actions = [
    "connect",
    "reconnect"
  ];

  /**
   * Client credentials like app id & secret.
   */
  protected ?array $credentials = null;

  /**
   * Override the construct method.
   */
  public function __construct()
  {

    # Set credentials to be available easily.
    $this->credentials = oauth_credentials(static::PROVIDER);

    return parent::__construct();
  }

  /**
   * @param object $params
   * @return self
   *
   * NOTE: Will die on error.
   */
  public function new(object $params)
  {

    /**
     * @var object
     */
    $response = $this->get_access_token(
      code: $params->code,
      action: "connect",
    );

    /**
     * $response->access_token
     * $response->refresh_token
     * $response->expires_in
     */

    /**
     * @var object
     */
    $ProviderUser = $this->provider_user($response->access_token);

    /**
     * Check, if a user with the given email address exists already. This could indi-
     * cate the user having either lost their credentials or trying to create a second
     * account on purpose.
     */
    if (
      !empty($ProviderUser->email)
      && User::where("email", $ProviderUser->email)->first()
    )
      return die(error("<strong>The e-mail address is not available.</strong> " . $this->dd["LOST_CREDENTIALS_RESET"]));

    # If a Connection already exists, the User or another one has already connected
    # the vendor's user account.
    if (
      self::where([
        "provider" => static::PROVIDER,
        "provider_user_id" => $ProviderUser->id
      ])
      ->whereNotNull("user_id")
      ->first()
    )
      return die(error("!API_CONNECTED_ALREADY"));

    /**
     * @var self
     */
    $Connection = self::where([
      "provider" => static::PROVIDER,
      "provider_user_id" => $ProviderUser->id
    ])
      ->whereNull("user_id")
      ->first()
      ?? self::make();

    $Connection->access_token = $response->access_token;
    $Connection->refresh_token = $response->refresh_token ?? null;
    $Connection->expires_at = !empty($response->expires_in)
      ? Time::add($response->expires_in)
      : null;
    $Connection->provider = static::PROVIDER;
    $Connection->provider_user_id = $ProviderUser->id;
    $Connection->provider_user_email = $ProviderUser->email ?? null;
    $Connection->provider_user_nickname = $ProviderUser->username;
    $Connection->is_legit = 0;

    # Evaluate if the user is a legit player by checkeing their rank against the top
    # 1000 players on the public osu! leaderboards.
    if (static::PROVIDER === "osu!") {
      foreach (ApiOsu::$rulesets as $ruleset) {
        $user_ids = $this->redis()
          ->sMembers(ApiRegistry::$redis_map["osu!"]["ranking"] . ":$ruleset");

        # Continue, if there is nothing cached which should not happen 😃.
        if (!$user_ids) continue;

        foreach ($user_ids as $user) {
          if ((int) $user === (int) $Connection->provider_user_id) {
            $Connection->is_legit = 1;
            break;
          }
        }
      }
    }

    # Save!
    $Connection->save();

    return $Connection;
  }

  /**
   * Retrieves an access token and fetches user information to confirm that the vendor
   * user is signed up to our application.
   *
   * @param object $params
   * @return static
   *
   * NOTE: Will die on error.
   */
  public function reconnect(object $params)
  {

    /**
     * @var object
     */
    $response = $this->get_access_token(
      code: $params->code,
      action: "reconnect",
    );

    /**
     * $response->access_token
     * $response->refresh_token
     * $response->expires_in
     */

    /**
     * @var object
     */
    $ProviderUser = $this->provider_user($response->access_token);

    /**
     * @var ?static
     */
    $Connection = static::with("user")
      ->where([
        "provider" => static::PROVIDER,
        "provider_user_id" => $ProviderUser->id
      ])
      ->whereNotNull("user_id")
      ->first()
      # If no Connection is found, die and tell the User to register first.
      ?? die(error("<strong>You have not signed up yet!</strong> <a href='/register'>Do here</a>, my friend."));

    return $Connection;
  }

  /**
   * Gets the API specifications for public access.
   *
   * @return ?array
   */
  public function scopes()
  {
    return (ApiRegistry::$map[static::PROVIDER ?: $this->provider]::$scopes)
      ?? null;
  }

  /**
   * Gets the API specifications for public access.
   *
   * @return ?array
   */
  public function api()
  {
    return (ApiRegistry::$map[static::PROVIDER ?: $this->provider]::$api)
      ?? null;
  }

  /**
   * Starts a curl request to a given API endpoint.
   *
   * @param string $api
   * @param array $data
   * @return ?object
   */
  public static function request(
    string $api,
    string $type = "POST",
    array $data = [],
    array $headers = []
  ) {

    $c = new static()->credentials;

    # Preconfigure the data array with client id and secret.
    $data["client_id"] = $c["client_id"];
    $data["client_secret"] = $c["client_secret"];

    # Preconfigure headers.
    $headers[] = "Accept: application/json";
    $headers[] = "Content-Type: application/x-www-form-urlencoded";

    return CURL::start(
      url: $api,
      type: $type,
      data: $data,
      headers: $headers,
    );
  }

  /**
   * Making a POST request to a given API endpoint.
   *
   * @param string $url
   * @param array $data
   * @param array $headers
   * @return ?object
   */
  public function post(
    string $url,
    array $data = [],
    array $headers = [],
  ) {

    # Preconfigure the data array with client id and secret.
    $data["client_id"] = $this->credentials["client_id"];
    $data["client_secret"] = $this->credentials["client_secret"];

    # Preconfigure headers.
    $headers[] = "Accept: application/json";
    $headers[] = "Content-Type: application/x-www-form-urlencoded";

    return CURL::start(
      url: $url,
      type: "POST",
      data: $data,
      headers: $headers,
    );
  }


  /**
   * Making a POST request to a given API endpoint.
   *
   * @param string $url
   * @param array $data
   * @param array $headers
   * @return ?object
   */
  public function get(
    string $url,
    array $data = [],
    array $headers = [],
  ) {

    # Preconfigure headers.
    $headers[] = "Accept: application/json";
    $headers[] = "Content-Type: application/x-www-form-urlencoded";

    return CURL::start(
      url: $url,
      type: "GET",
      data: $data,
      headers: $headers,
    );
  }

  /**
   * @return void
   *
   * NOTE: Will die one error.
   */
  public function action_invalid(string $action)
  {
    if (!in_array($action, $this->actions))
      die(error("<strong>Invalid action called!</strong>"));
  }

  /**
   * Generates the link to the vendor's API where the user has to authorize their ac-
   * count.
   *
   * @param string $action
   * @return string
   */
  public function generate_link(string $action = "connect")
  {

    # Die immediately if an invalid action has been called.
    $this->action_invalid($action);

    $callback = _env("SERVER_ADDRESS") . "/$action/" . static::PROVIDER;
    $return = $this->api()["user-auth"]["endpoint"]
      . "?client_id=" . $this->credentials["client_id"]
      . "&response_type=" . $this->api()["user-auth"]["response_type"]
      . "&redirect_uri=" . $callback
      . "&state=" . Utils::random_alpha_token(124)
      . "&scope=" . implode(" ", $this->scopes());

    return $return;
  }

  /**
   * Fetches a new access_token for a vendor's user on their api. We always need a
   * code for this as we fetch with user specific grant.
   *
   * @param string $code
   * @param string $action
   * @return object
   *
   * NOTE: Will die on error.
   */
  public function get_access_token(
    string $code,
    string $action = "connect"
  ) {

    # Die immediately if an invalid action has been called.
    $this->action_invalid($action);

    # Build dataset.
    $data = [];
    $data["code"] = $code;
    $data["redirect_uri"] = _env("SERVER_ADDRESS") . "/$action/" . static::PROVIDER;

    # Apply a grant_type if one is set in specifications.
    if ($this->api()["user-access"]["grant_type"] ?? false)
      $data["grant_type"] = $this->api()["user-access"]["grant_type"];

    $response = $this->post(
      url: $this->api()["user-access"]["endpoint"],
      data: $data,
    );

    # No access token given?
    if (empty($response->access_token))
      return die(error("Could not retreive access token from " . static::PROVIDER . " api."));

    return $response;
  }
}
