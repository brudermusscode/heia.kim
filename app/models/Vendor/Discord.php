<?php

namespace Bruder\Heiakim\Model\Vendor;

use Bruder\Utils\Utils;

class Discord extends Vendor implements VendorInterface
{

  public ?string $access_token = null;

  public ?string $refresh_token = null;

  public ?object $user = null;

  public ?object $auth = null;

  /**
   * @param ?object $auth
   * @return void
   */
  public function __construct(?object $auth = null)
  {
    $this->credentials = $this->oauth_credentials("discord");

    if ($auth && isset($auth->access_token, $auth->refresh_token)) {
      $this->access_token = $auth->access_token;
      $this->refresh_token = $auth->refresh_token;
      $this->auth = $auth;
      $this->user = $this->get_user();
    }
  }

  /**
   * @return void
   */
  public function get_client() {}

  /**
   * @param ?object $params
   * @return object
   */
  public function get_auth_uri(?object $params = null)
  {
    $redirect_uri = $this->credentials[current_env()]["return_uris"]["connect"];
    $return = $this->credentials["auth_url"]
      . "?response_type=code"
      . "&client_id=" . $this->credentials["client_id"]
      . "&scope=" . $this->credentials["scope"]
      . "&state=" . Utils::random_alpha_token(124)
      . "&redirect_uri=" . $redirect_uri
      . "&prompt=consent";

    return request_success(data: $return);
  }

  /**
   * @param object $param
   * @return string|self
   */
  public function new(object $params)
  {

    /**
     * @var array
     */
    $data = [
      "client_id" => $this->credentials["client_id"],
      "client_secret" => $this->credentials["client_secret"],
      "grant_type" => "authorization_code",
      "code" => $params->code,
      "redirect_uri" => $this->credentials[current_env()]["return_uris"]["connect"],
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $this->credentials["token_url"]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);

    /**
     * Request failed?
     */
    if (!isset($results["refresh_token"]) || !empty($results["error"]))
      return request_error("<strong>Couldn't fetch your credentials.</strong>");

    /**
     * @var array
     */
    $refreshData = [
      'client_id' => $this->credentials["client_id"],
      'client_secret' => $this->credentials["client_secret"],
      'grant_type' => 'refresh_token',
      'refresh_token' => $results["refresh_token"],
      'scope' => 'identify email', // Adjust scopes as needed
    ];

    $ch = curl_init($this->credentials["token_url"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $refreshData);

    $refreshResponse = curl_exec($ch);
    curl_close($ch);

    $refreshTokenData = json_decode($refreshResponse, true);

    /**
     * Request valid?
     */
    if (!isset($refreshTokenData['access_token']) || !empty($results["error"]))
      return request_error("<strong>Couldn't fetch new refresh-token.</strong>");

    return new self((object) $refreshTokenData);
  }

  /**
   * @param object $param
   * @return object
   */
  public function success(object $params) {}

  /**
   * @param object $param
   * @return object
   */
  public function update_tokens(object $params)
  {
    // Step 3: Exchange Authorization Code for Tokens
    $credentials = $this->credentials;
    $postData = [
      'client_id' => $credentials["client_id"],
      'client_secret' => $credentials["client_secret"],
      'grant_type' => 'authorization_code',
      'code' => $params->code,
      'redirect_uri' => $credentials[current_env()]["return_uris"]["return_url"],
      'scope' => 'identify email',
    ];

    $ch = curl_init($credentials["token_url"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    curl_close($ch);

    // Parse the response
    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
      $accessToken = $tokenData['access_token'];
      $refreshToken = $tokenData['refresh_token'];

      // Step 4: Use Refresh Token to obtain a new access token
      $refreshData = [
        'client_id' => $credentials["client_id"],
        'client_secret' => $credentials["client_secret"],
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
        'scope' => 'identify email',
      ];

      $ch = curl_init($credentials["token_url"]);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $refreshData);

      $refreshResponse = curl_exec($ch);
      curl_close($ch);

      // Parse the refresh response
      $refreshTokenData = json_decode($refreshResponse, true);

      if (isset($refreshTokenData['access_token'])) {
        $newAccessToken = $refreshTokenData['access_token'];

        // Now $newAccessToken can be used for authenticated requests
        echo 'New Access Token: ' . $newAccessToken;
      } else {
        echo 'Error refreshing access token';
      }
    } else {
      echo 'Error exchanging authorization code for tokens';
    }
  }

  /**
   * @return void
   */
  public function get_user()
  {
    $url = $this->credentials["base_url"] . "/api/users/@me";
    $headers = [
      'Content-Type: application/x-www-form-urlencoded',
      'Authorization: Bearer ' . $this->access_token
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);

    /**
     * Request valid?
     */
    if (!$results || !empty($results["error"]))
      return null;

    /**
     * Objectify the results.
     */
    $results = (object) $results;
    $results->avatar_url = $results->avatar
      ? $this->credentials["avatar_url"] . "/" . $results->id . "/" . $results->avatar . ".jpg"
      : null;

    return $results;
  }

  /**
   * @param int $guild_id
   * @param ?int $user_id
   * @return bool
   */
  public function join_server(?int $guild_id = null, int $user_id = 0)
  {

    /**
     * @var int
     */
    $guild_id = $guild_id ?? $this->credentials["guild_id"];
    $user_id = ($user_id !== 0 ? $user_id : $this->user?->id) ?? 0;

    $url = $this->credentials["base_url"] . "/api/guilds/$guild_id/members/" . $user_id;
    $data = json_encode([
      "access_token" => $this->access_token,
    ]);
    $headers = [
      'Content-Type: application/json',
      'Authorization: Bot ' . $this->credentials["bot_token"],
    ];

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($curl);

    curl_close($curl);

    $results = json_decode($response, true);

    /**
     * Request failed?
     */
    if (!$results || !empty($results["error"]))
      return false;

    return true;
  }

  /**
   * @param int $role_id
   * @param int $user_id
   * @return bool
   */
  public function give_role(int $role_id, int $user_id = 0)
  {
    /**
     * @var int
     */
    $guild_id = $this->credentials["guild_id"];

    /**
     * @var int
     */
    $user_id = ($user_id !== 0 ? $user_id : $this->user?->id) ?? 0;

    $url = $this->credentials["base_url"] . "/api/guilds/$guild_id/members/" . $user_id . "/roles/$role_id";
    $data = json_encode([
      "roles" => [
        $role_id
      ]
    ]);
    $headers = [
      'Content-Type: application/json',
      'Authorization: Bot ' . $this->credentials["bot_token"],
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);

    /**
     * Request failed?
     */
    if (!$results || !empty($results["error"]))
      return false;

    return true;
  }

  /**
   * @param int $role_id
   * @param int $user_id
   * @return bool
   */
  public function take_role(int $role_id, int $user_id = 0)
  {
    /**
     * @var int
     */
    $guild_id = $this->credentials["guild_id"];

    /**
     * @var int
     */
    $user_id = ($user_id !== 0 ? $user_id : $this->user?->id) ?? 0;

    $url = $this->credentials["base_url"] . "/api/guilds/$guild_id/members/" . $user_id . "/roles/$role_id";
    $data = json_encode([
      "roles" => [
        $role_id
      ]
    ]);
    $headers = [
      'Content-Type: application/json',
      'Authorization: Bot ' . $this->credentials["bot_token"],
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);

    /**
     * Request failed?
     */
    if (!$results || !empty($results["error"]))
      return false;

    return true;
  }
}
