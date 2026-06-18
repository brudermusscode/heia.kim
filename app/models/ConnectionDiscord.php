<?php

/**
 * This class represents a connection to an existing Discord account.
 */

namespace Heiakim\Model;

use Heiakim\Time\Time;
use Heiakim\Trait\IsConnectionProvider;

class ConnectionDiscord extends Connection
{
  use IsConnectionProvider;

  /**
   * @see https://discord.com
   */
  protected const string PROVIDER = "discord";

  /**
   * @see https://docs.discord.com/developers/topics/oauth2#authorization-code-grant
   * @method generate_link();
   */

  /**
   * @see https://docs.discord.com/developers/topics/oauth2#authorization-code-grant
   * @method get_access_token();
   */

  /**
   * @method new();
   * $response->access_token
   * $response->refresh_token
   * $response->expires_in
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

    $response = $this->post(
      url: $this->api()["user-refresh-access"]["endpoint"],
      data: [
        "grant_type" => $this->api()["user-refresh-access"]["grant_type"],
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
   * Fetches the information of the user that has authorized us fetching their
   * information using the received access_token.
   *
   * @param ?string $access_token
   * @return ?object
   * @see https://docs.discord.com/developers/resources/user#get-current-user
   *
   * NOTE: Will die on error.
   */
  public function provider_user(?string $access_token = null)
  {

    # Make a request to get user information.
    $response = $this->get(
      url: $this->api()["general"]["endpoint"] . "/users/@me",
      headers: [
        'Authorization: Bearer ' . ($this->access_token ?? $access_token),
      ],
    );

    # Reponse has no user id and thus failed?
    if (empty($response->id))
      die(error("Could not fetch user of provider " . static::PROVIDER));

    return $response;
  }

  # TODO: Add to heia.kim server automatically.
}
