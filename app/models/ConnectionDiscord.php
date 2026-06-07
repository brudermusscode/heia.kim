<?php

/**
 * This class represents a connection to an existing Discord account.
 */

namespace Heiakim\Model;

use Heiakim\Model\Vendor\Discord;
use Heiakim\Registry\ApiRegistry;
use Heiakim\Time\Time;
use Heiakim\Trait\IsConnectionProvider;
use Heiakim\Utils\Utils;
use Heiakim\Http\CURL;

class ConnectionDiscord extends Connection
{
  use IsConnectionProvider;

  /**
   * @see https://osu.ppy.sh
   */
  protected const string PROVIDER = "discord";

  /**
   * @see https://docs.discord.com/developers/topics/oauth2#authorization-code-grant
   * generate_link();
   */

  /**
   * @see https://docs.discord.com/developers/topics/oauth2#authorization-code-grant
   * get_access_token();
   */

  /**
   * Based on a refresh_token being set on this instance, refreshes the access token
   * by starting a new cURL request to the osu! API and sets them on this instance.
   *
   * @return static|false
   */
  public function refresh_access_token()
  {

    # Check for the instance's refresh_token. We can return instantly if none is set,
    # as we would need new user authorization for obatining a new code. This is not
    # possible here.
    if (!$this->refresh_token)
      return false;

    $api = $this->api();
    $this->request(
      api: $api["user-refresh-access"]["endpoint"],
      data: [
        "grant_type" => $api["user-refresh-access"]["grant_type"],
        "refresh_token" => $this->refresh_token,
      ],
    );

    /**
     * $response->access_token
     * $response->refresh_token
     * $response->expires_in
     */

    # No access token is given from cURL request?
    if (empty($response->access_token))
      die(error("!INVALID_API_CALL"));

    $this->access_token = $response->access_token;
    $this->refresh_token = $response->refresh_token;
    $this->expires_at = Time::add($response->expires_in);

    # Save it!
    $this->save();

    return $this;
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
    $Connection->refresh_token = $response->refresh_token;
    $Connection->expires_at = Time::add($response->expires_in);
    $Connection->provider = static::PROVIDER;
    $Connection->provider_user_id = $ProviderUser->id;
    $Connection->provider_user_email = $ProviderUser->email;
    $Connection->provider_user_nickname = $ProviderUser->username;
    $Connection->is_legit = 0;

    # Save!
    $Connection->save();

    return $Connection;
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

    $api = $this->api();
    $response = CURL::start(
      url: $api["general"]["endpoint"] . "/users/@me",
      type: "GET",
      options: [
        CURLOPT_HTTPHEADER => [
          'Accept: application/json',
          'Content-Type: application/x-www-form-urlencoded',
          'Authorization: Bearer ' . ($this->access_token ?? $access_token),
        ],
      ],
    );

    # Reponse has no user id and thus failed?
    if (empty($response->id))
      die(error("!INVALID_API_CALL"));

    return $response;
  }

  # TODO: Add to heia.kim server automatically.
}
