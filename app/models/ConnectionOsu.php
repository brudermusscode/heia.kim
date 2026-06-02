<?php

/**
 * This class represents a connection to an existing osu! account.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\Session;
use Heiakim\Model\Vendor\Osu;
use Heiakim\Utils\Utils;
use Heiakim\Trait\IsProviderConnection;

class ConnectionOsu extends Justin
{
  use IsProviderConnection;

  /**
   * @see https://osu.ppy.sh/docs/#scopes
   */
  protected static array $scopes = [
    "identify" => "identify",
    "public" => "public",
  ];

  /**
   * @see https://osu.ppy.sh/docs/#ruleset
   */
  protected static array $rulesets = [
    "osu",
    "taiko",
    "mania",
    "fruits"
  ];

  private ?string $access_token = null;

  /**
   * Generates the link to the vendor's API where the user has to auth-
   * orize their account.
   *
   * @return string
   * @see https://osu.ppy.sh/docs/#authorization-code-grant
   */
  public static function generate_link()
  {

    $credentials = self::oauth_credentials();
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
    $credentials = self::oauth_credentials();
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
    $ProviderUser = $this->provider_user();

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
    $Connection->expires_at = gmdate("Y-m-d H:i:s", time() + $response->expires_in);
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
    /**
     * Create a new request to the API.
     */
    $Osu = $this->fetch_credentials_with_code($params);

    /**
     * Credentials valid?
     */
    if (!($Osu instanceof Osu))
      return $Osu;

    /**
     * @var ?ConnectOsu
     */
    $Connect = ConnectOsu::whereNotNull("user_id")
      ->where("vendor_id", $Osu->user->id)
      ->first();

    /**
     * Account is signed up already with this vendor?
     */
    if (!$Connect)
      return $this->error("<strong>Couldn't find this account.</strong>");

    /**
     * Create a session!
     */
    return (new Session)->new((object) [
      "user_id" => $Connect->user_id,
    ]);
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

    $credentials = self::oauth_credentials();
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

  /**
   * Utilizes a cURL request to fetch the leaderboards from official osu! ser-
   * vers. It will return the $count for ANY ruleset passed, so when, for ex-
   * ample fetching all rulesets (4), it will return an array of 400 user_ids.
   *
   * @param int $count 50, 100, 150, …
   * @param ?string $rulesets osu, taiko, mania, fruits
   * @return array of user_ids
   */
  public function leaderboard(int $count = 50, ?array $rulesets = null)
  {

    $user_ids = [];

    # One request will atleast fetch 50 players.
    if ($count < 50)
      $count = 50;

    # Pages to iterate through can be determined by the set count, when
    # divisible by 50. Otherwise we fallback to 1.
    $pages = ($count % 50 === 0 ? $count / 50 : 1);

    # Based on the mode set in params, we iterate through either the one
    # set or when null, all.
    foreach ((!$rulesets ? self::$rulesets : $rulesets) as $ruleset)
      for ($page = 1; $page <= $pages; $page++) {
        $top50 = $this->top_50(mode: $ruleset, page: $page);
        foreach ($top50 ?? [] as $rank)
          $user_ids[] = $rank->user->id;

        # osu! permits one request per second, so throttle the execu-
        # tion by exactly one second 🙂.
        sleep(1);
      }

    return $user_ids;
  }

  /**
   * Fetch leaderboards from osu! API. One request will return 50 players.
   *
   * @param string $mode
   * @param int $page
   * @param ?string $access_token
   * @return ?object
   * @see https://osu.ppy.sh/docs/#get-ranking
   *
   */
  public function top_50(string $mode = "osu", int $page = 1, ?string $access_token = null)
  {

    # Build data & headers for the comming cURL request.
    $credentials = self::oauth_credentials();
    $headers = [
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Bearer ' . ($this->access_token ?? $access_token),
    ];

    # Start cURL request as GET.
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $credentials["base_url"] . "/rankings/$mode/performance?cursor[page]=$page",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($curl);
    $response = json_decode($response);

    # cURL failed indicated by no user inside a ranking object is set?
    if (empty($response->ranking[0]->user))
      return null;

    return $response->ranking;
  }
}
