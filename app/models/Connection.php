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
  public static function request(string $api, array $data)
  {

    $c = new static()->credentials;

    # Preconfigure the data array with client id and secret.
    $data["client_id"] = $c["client_id"];
    $data["client_secret"] = $c["client_secret"];

    return CURL::start(
      url: $api,
      data: $data,
      options: [
        CURLOPT_HTTPHEADER => $c["authorization_headers"],
      ],
      // debug: true,
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

    $api = $this->api();
    $callback = $this->credentials["callback"][current_env()]["connect"];
    $return = $api["user-auth"]["endpoint"]
      . "?client_id=" . $this->credentials["client_id"]
      . "&response_type=" . $api["user-auth"]["response_type"]
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

    $api = $this->api();
    $response = static::request(
      api: $api["user-access"]["endpoint"],
      data: [
        "code" => $code,
        "grant_type" => $api["user-access"]["grant_type"],
        "redirect_uri" => $this->credentials["callback"][current_env()][$action],
      ],
    );

    # cURL request failed based on no access token is given?
    if (empty($response->access_token))
      die(error("!INVALID_API_CALL"));

    return $response;
  }
}
