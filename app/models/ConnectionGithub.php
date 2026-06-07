<?php

/**
 * This class represents a connection to an existing GitHub account.
 */

namespace Heiakim\Model;

use Heiakim\Registry\ApiRegistry;
use Heiakim\Time\Time;
use Heiakim\Trait\IsConnectionProvider;
use Heiakim\Http\CURL;

class ConnectionGithub extends Connection
{
  use IsConnectionProvider;

  /**
   * @see https://github.com
   */
  protected const string PROVIDER = "github";

  /**
   * @see https://docs.github.com/en/apps/oauth-apps/building-oauth-apps/authorizing-oauth-apps#1-request-a-users-github-identity
   * @method generate_link();
   */

  /**
   * @see https://docs.github.com/en/apps/oauth-apps/building-oauth-apps/authorizing-oauth-apps#2-users-are-redirected-back-to-your-site-by-github
   * @method get_access_token();
   */

  /**
   * @method new();
   * $response->access_token
   * $response->token_type
   * $response->scope
   */

  /**
   * NOTE: Not needed, as tokens do not expire, unless we revoke them manually.
   * @method refresh_access_token();
   */

  /**
   * @param object $params
   * @return object
   */
  public function login(object $params) {}

  /**
   * Fetches the information of the user that has authorized us fetching their
   * information using the received access_token.
   *
   * @param ?string $access_token
   * @return ?object
   * @see https://docs.github.com/en/rest/users/users
   *
   * NOTE: Will die on error.
   */
  public function provider_user(?string $access_token = null)
  {

    $headers = [
      'Authorization: Bearer ' . ($this->access_token ?? $access_token),
    ];

    # First request to get user information.
    $response = $this->get(
      url: $this->api()["general"]["endpoint"] . "/user",
      headers: $headers,
    );

    # Reponse has no user id and thus failed?
    if (empty($response->id))
      die(error("Could not receive user from " . static::PROVIDER . " api"));

    # Set the login to username, which is the username.
    $response->username = $response->login;

    # Early return if an email is set here.
    if ($response->email)
      return $response;

    $ProviderUser = $response;

    # Make another request for an email address.
    $response = $this->get(
      url: $this->api()["general"]["endpoint"] . "/user/emails",
      headers: $headers,
    );

    # Reponse has no user id and thus failed?
    if (empty($response))
      die(error("Could not receive user emails from " . static::PROVIDER . " api"));

    /**
     * An array of objects with all emails from github will be returned.
     * $response[0]->email
     * $response[0]->primary
     * $response[0]->verified
     * $response[0]->visibility
     */

    # Search through all email addresses for one that is set as primary & verified and set it to the $ProviderUser.
    foreach ($response as $email)
      if ($email->verified && $email->primary)
        $ProviderUser->email = $email->email;

    return $ProviderUser;
  }
}
