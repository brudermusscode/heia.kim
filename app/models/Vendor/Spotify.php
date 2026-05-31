<?php

namespace Heiakim\Model\Vendor;

use Heiakim\Model;
use Heiakim\Http\Request;
use Heiakim\Application\Cookie;
use Heiakim\Database\Manager as DBM;
use Heiakim\Justin;
use Heiakim\Model\User;
use SpotifyWebAPI;
use DateTime;

class Spotify extends Justin
{
  /**
   * @var string
   */
  protected $table = "api_spotify";

  /**
   * The URI to redirect successful or failed requests.
   *
   * @var string
   */
  private static $redirect_uri = "/api-calls/spotify/callback";

  /**
   * Cookie to save the state in.
   *
   * @var string
   */
  private static $cookie = "API_CALL_SPOTIFY_STATE";

  /**
   * Getter for the connection to the Spotify API using
   * credentials from model variables.
   *
   * @return object The connection object.
   */
  public function get_connection()
  {
    $credentials = self::get_oauth_credentials("spotify");

    return new SpotifyWebAPI\Session(
      $credentials->client_id,
      $credentials->client_secret,
      $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . self::$redirect_uri,
    );
  }

  /**
   * Starts authentication and sets a cookie on success, saving
   * the state for callback.
   *
   * @param object $params The params with state and code.
   * @return object Default return object.
   */
  public function authenticate(object $params)
  {
    /**
     * Check if user is connected already.
     */
    if (self::user_is_authenticated($params->user_id))
      return $this->error("<strong>You are already connected with Spotify!</strong>");

    $session = $this->get_connection();
    $state = $session->generateState();
    $options = [
      'scope' => [
        'playlist-read-private',
        'user-read-private',
      ],
      'state' => $state,
    ];

    $this->return->data = (object) [];
    $this->return->data->auth_url = $session->getAuthorizeUrl($options);
    $this->return->data->state = $state;

    /**
     * Set a cookie with the state to be stored for 20 minutes.
     */
    Cookie::set(self::$cookie, $state, "+20 Minutes");

    return $this->success("<strong>Authentication has been requested!</strong> Go on and accept the following dialogue.");
  }

  /**
   * The callback function when the user returns from the spotify
   * authentication window.
   *
   * @param object $params The params with state and code.
   * @return object Default return object.
   */
  public function callback(object $params)
  {
    $session = $this->get_connection();
    $state = $params->state;
    $state_stored = Cookie::get(self::$cookie);

    /**
     * Check if the stored state is valid.
     */
    if ($state !== $state_stored)
      return $this->error("<strong>A mismatch in states was found.</strong>");

    /**
     * Check if user is connected already.
     */
    if (self::user_is_authenticated($params->user_id))
      return $this->error("<strong>You are already connected with Spotify!</strong>");

    /**
     * Remove the stored state cookie.
     */
    // Cookie::delete(self::$cookie);

    /**
     * Request access token from Spotify API.
     */
    try {
      $session->requestAccessToken($params->code);
    } catch (\Exception $e) {
      return $this->error("<strong>An error occured while obtaining your access code.</strong>");
    }

    $this->db->beginTransaction();

    /**
     * Insert data for user.
     */
    $table = self::$table;
    $stmt = $this->db->insert(
      "INSERT INTO $table (user_id, access_token, refresh_token) VALUES (?, ?, ?)",
      [$params->user_id, $session->getAccessToken(), $session->getRefreshToken()],
    );

    if (!$stmt) {
      $this->db->rollback();

      return $this->error("<strong>An error occured while calling back to our application.</strong>");
    }

    $this->db->commit();

    return $this->success("<strong>You have connected with Spotify!</strong>");
  }

  /**
   * Checks whether or not the user requesting to connect with
   * spotify is authenticated already.
   *
   * @param int $user_id The id of the user requesting.
   * @return bool Whether or not.
   */
  public static function user_is_authenticated(int $user_id)
  {
    $User = new User($user_id);

    return $User->data() && $User->get_spotify_credentials();
  }

  /**
   * Check if the token of a given user is outdated (by 1 hour, as
   * OAuth 2 guidelines give as a rule for security)
   *
   * @return bool Whether or not the token is older than 1 hour.
   */
  public function is_access_token_expired()
  {
    /**
     * Check if user is connected.
     */
    if (!$this->data()) {
      echo "no data";
      return true;
    }

    /**
     * Create the necvessary timestamps from token and current time.
     */
    $format = 'Y-m-d H:i:s';
    $token_timestamp_consideration =
      $this->data()->updated_at
      ? $this->data()->updated_at
      : $this->data()->created_at;

    $token_timestamp = DateTime::createFromFormat($format, $token_timestamp_consideration);
    $current_timestamp = DateTime::createFromFormat($format, date($format));

    /**
     * If if the access token is older than one hour.
     */
    $diff = $current_timestamp->diff($token_timestamp);

    return ($diff->h < 1 && $diff->i > 52) || $diff->h >= 1;
  }

  /**
   * Edit the current instance.
   *
   * @param object $params the params to edit with.
   * @return object Default return object.
   */
  public function edit(object $params)
  {
    $this->update((array) $params);

    return $this->success("<strong>Your Spotify API tokens have been refreshed!</strong>");
  }
}
