<?php

/**
 * This class represents a connection to an existing osu! account.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Time\Time;
use Heiakim\Utils\Utils;
use Heiakim\Trait\IsConnectionProvider;

class ConnectionOsu extends Justin
{
  use IsConnectionProvider;

  /**
   * @see https://osu.ppy.sh
   */
  protected string $provider = "osu!";

  /**
   * @see https://osu.ppy.sh/docs/#scopes
   */
  protected static array $scopes = [
    "identify" => "identify",
    "public" => "public",
  ];

  /**
   * Generates the link to the vendor's API where the user has to auth-
   * orize their account.
   *
   * @return string
   * @see https://osu.ppy.sh/docs/#authorization-code-grant
   */
  public static function generate_link()
  {

    $credentials = oauth_credentials(static::$provider);
    $callback = $credentials["callback"][current_env()]["connect"];
    $return = $credentials["auth_url"]
      . "?client_id=" . $credentials["client_id"]
      . "&redirect_uri=" . $callback
      . "&response_type=code"
      . "&state=" . Utils::random_alpha_token(124)
      . "&scope=" . implode(" ", self::$scopes)
      . "&provider=" . self::provider_map_key();

    return $return;
  }

  /**
   * @param object $params
   * @return self
   * @see https://osu.ppy.sh/docs/#authorization-code-grant
   *
   * NOTE: Will die on error.
   */
  public function new(object $params)
  {

    # Build data & headers for the comming cURL request.
    $credentials = oauth_credentials(static::$provider);
    $data = [
      "client_id" => $credentials["client_id"],
      "client_secret" => $credentials["client_secret"],
      "code" => $params->code,
      "grant_type" => "authorization_code",
      "redirect_uri" => $credentials["callback"][current_env()]["connect"],
    ];

    $headers = [
      'Accept: application/json',
      'Content-Type: application/x-www-form-urlencoded',
    ];

    # Start cURL request.
    $curl = curl_init();
    curl_setopt_array($curl, [
      // CURLOPT_VERBOSE => true,
      CURLOPT_URL => $credentials["token_url"],
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query($data),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers
    ]);

    # For development environment without SSL, we need to disable
    # SSL specific validations for cURL requests.
    if (current_env() === "dev") {
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $response = curl_exec($curl);
    $response = json_decode($response);

    # cURL request failed based on no access token is given?
    if (empty($response->access_token))
      die(error("!INVALID_API_CALL"));

    /**
     * $response->access_token
     * $response->refresh_token
     * $response->expires_in
     */

    # Set the access token to be global inside this instance.
    $this->access_token = $response->access_token;

    /**
     * @var object
     */
    $ProviderUser = $this->provider_user($response->access_token);

    # If a Connection already exists, the User or another one has already con-
    # nected the vendor's user account.
    if (
      self::where([
        "provider" => self::provider_map_key(),
        "provider_user_id" => $ProviderUser->id
      ])->first()
    )
      die(error("!API_CONNECTED_ALREADY"));

    /**
     * @var self
     */
    $Connection = self::make();
    # TODO: Access token is not saved somehow?
    $Connection->access_token = $response->access_token;
    $Connection->refresh_token = $response->refresh_token;
    $Connection->expires_at = Time::add($response->expires_in);
    $Connection->provider = self::provider_map_key();
    $Connection->provider_user_id = $ProviderUser->id;
    $Connection->provider_user_email = null;
    $Connection->provider_user_nickname = $ProviderUser->username;

    # Evaluate if the user is a legit player by checkeing their rank against
    # the top 100 players on the public osu! leaderboards. Skip in dev mode.
    $leaderboard = current_env() !== "dev" ? $this->leaderboard(count: 100) : null;

    # Set the legitimacy and save the Connection!
    $Connection->is_legit = $leaderboard && in_array($ProviderUser->id, $leaderboard) ?: null;
    $Connection->save();

    return $Connection;
  }

  /**
   * @param object $params
   * @return object
   */
  public function login(object $params)
  {
    $credentials = oauth_credentials(static::$provider);
  }

  /**
   * Fetches the information of the user that has authorized us fetching their
   * information using the received access_token.
   *
   * @param ?string $access_token
   * @return ?object
   * @see https://osu.ppy.sh/docs/#account
   *
   * NOTE: Will die on error.
   */
  public function provider_user(?string $access_token = null)
  {

    $credentials = oauth_credentials(static::$provider);
    $headers = [
      'Accept: application/json',
      'Content-Type: application/x-www-form-urlencoded',
      'Authorization: Bearer ' . ($this->access_token ?? $access_token),
    ];

    # Start cURL request as a GET request.
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $credentials["base_url"] . "/me",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
    ]);

    $response = curl_exec($curl);
    $response = json_decode($response);

    # Reponse has no user id and thus failed?
    if (empty($response->id))
      die(error("!INVALID_API_CALL"));

    return $response;
  }
}
