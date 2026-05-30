<?php

namespace Bruder\Heiakim\Model\Vendor;

use Bruder\Utils\Utils;

class Osu extends Vendor implements VendorInterface
{

  protected array $scopes = [
    "identify",
    "public",
  ];

  public ?string $access_token = null;

  public ?string $refresh_token = null;

  public ?object $user = null;

  public ?object $auth = null;

  public ?array $top_players = null;

  /**
   * @param ?object $auth
   * @return void
   */
  public function __construct(?object $auth = null)
  {
    $this->credentials = $this->oauth_credentials("osu");

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
   * @param object $param
   * @return object
   */
  public function get_auth_uri(?object $params = null)
  {
    $redirect_uri = $this->credentials["redirect_uris"][current_env()]["return_uris"]["connect"];
    $return = $this->credentials["auth_url"]
      . "?client_id=" . $this->credentials["client_id"]
      . "&redirect_uri=" . $redirect_uri
      . "&response_type=code"
      . "&state=" . Utils::random_alpha_token(124)
      . "&scope=" . implode(" ", $this->scopes);

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
      "redirect_uri" => $this->credentials["redirect_uris"][current_env()]["return_uris"]["connect"],
      "scope" => implode(" ", $this->scopes),
    ];

    /**
     * @var array
     */
    $headers = [
      'Content-Type: application/x-www-form-urlencoded',
      'Accept: application/json',
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $this->credentials["token_url"]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);

    /**
     * @var array
     */
    $results = json_decode($response, true);

    /**
     * Results are valid?
     */
    if (!isset($results["access_token"]))
      return request_error("<strong>Couldn't fetch your credentials.</strong>");

    return new Self((object) $results);
  }

  /**
   * @return ?object
   */
  public function get_user()
  {

    $headers = [
      'Content-Type: application/x-www-form-urlencoded',
      'Authorization: Bearer ' . $this->access_token,
      'Accept: application/json',
    ];
    $url = $this->credentials["base_url"] . "/me";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);

    /**
     * @var array
     */
    $results = json_decode($response, true);

    /**
     * Request failed?
     */
    if (!$results["id"] || isset($results["error"]))
      return null;

    return (object) $results;
  }

  /**
   * @return array
   */
  public function get_valuable_top_players_of_all_modes()
  {
    /**
     * @var array
     */
    $user_ids = [];

    /**
     * Iterate through all modes and get the first 100 top players
     * and append them to the user_ids array.
     */
    foreach (["osu", "mania", "taiko", "fruits"] as $mode) {
      $first = $this->get_50_top_players(mode: $mode);
      foreach ($first["ranking"] ?? [] as $key => $user)
        $user_ids[] = $user["user"]["id"];

      $second = $this->get_50_top_players(mode: $mode, page: 2);
      foreach ($second["ranking"] ?? [] as $key => $user)
        $user_ids[] = $user["user"]["id"];
    }

    /**
     * Append me.
     */
    $user_ids[] = 25367973;

    return $user_ids;
  }

  /**
   * @return ?object
   */
  public function get_50_top_players(string $mode = "osu", ?int $page = null)
  {
    $headers = [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $this->access_token,
      'Accept: application/json',
    ];
    $url = $this->credentials["base_url"] . "/rankings/$mode/performance" . ($page ? "?cursor[page]=" . $page : "");

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    // curl_setopt($curl, CURLOPT_POST, true);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);

    /**
     * @var array
     */
    $results = json_decode($response, true);

    if (!isset($results["ranking"]))
      return null;

    return $results;
  }

  /**
   * @param object $param
   * @return object
   */
  public function success(object $params) {}
}
