<?php

/**
 * This class represents any connection to a vendor.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Trait\IsConnectionProvider;
use Heiakim\Http\CURL;
use Heiakim\Registry\ApiRegistry;
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
   * Generates the link to the vendor's API where the user has to authorize their ac-
   * count.
   *
   * @return string
   */
  public function generate_link()
  {

    $callback = _env("SERVER_ADDRESS") . "/connect/" . static::PROVIDER;
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
