<?php

/**
 * Interact with Discord API in general fashion.
 */

namespace Heiakim\Model;

use Heiakim\Time\Time;
use Heiakim\Http\CURL;

class ApiDiscord extends ApiProvider
{

  /**
   * @see https://discord.com
   */
  protected const PROVIDER = "discord";

  /**
   * Discord API specifications.
   */
  public static array $api = [
    "general" => [
      "endpoint" => "https://discord.com/api/v10",
    ],
    "auth" => [
      "endpoint" => "https://discord.com/api/oauth2/token",
      "grant_type" => "client_credentials",
    ],
    "user-auth" => [
      "endpoint" => "https://discord.com/oauth2/authorize",
      "response_type" => "code",
    ],
    "user-access" => [
      "endpoint" => "https://discord.com/api/oauth2/token",
      "grant_type" => "authorization_code"
    ],
    "user-refresh-access" => [
      "endpoint" => "https://discord.com/api/oauth2/token",
      "grant_type" => "refresh_token",
    ],
  ];

  /**
   * @see https://docs.discord.com/developers/platform/oauth2-and-permissions#scopes
   */
  public static array $scopes = [
    "identify" => "identify",
    "email" => "email",
    "guilds" => "guilds",
    "guilds.join" => "guilds.join",
  ];

  /**
   * Refreshes the access token of the current instance. If a valid one is
   * still available, it will return the current instance without fetching
   * a new one, unless forced.
   *
   * @param bool $force
   * @return ?static
   * @see https://docs.discord.com/developers/topics/oauth2#client-credentials-grant
   */
  public function refresh_access_token(bool $force = false)
  {

    # TODO: Make this work!

    # Return the current instance right away, if there is an access token which
    # should not have expired yet. But skip, if forced!
    if ($this->access_token && !Time::over($this->expires_at) && !$force)
      return $this;

    $c = oauth_credentials(static::PROVIDER);
    $response = CURL::start(
      url: static::$api["auth"]["endpoint"],
      data: [
        "client_id" => $c["client_id"],
        "client_secret" => $c["client_secret"],
        "grant_type" => static::$api["auth"]["grant_type"],
        "scope" => static::$scopes["identify"],
      ],
      options: [
        CURLOPT_HTTPHEADER => [
          'Accept: application/json',
          'Content-Type: application/x-www-form-urlencoded',
        ],
      ],
      // debug: true,
    );

    # cURL request failed based on no access token is given?
    if (empty($response->access_token))
      die(error("!INVALID_API_CALL"));

    $this->access_token = $response->access_token;
    $this->expires_at = Time::add($response->expires_in);
    $this->save();

    return $this;
  }
}
