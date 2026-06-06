<?php

/**
 * Interact with osu! API in general fashion.
 */

namespace Heiakim\Model;

use Heiakim\Exception\ApiException;
use Heiakim\Registry\ApiRegistry;
use Heiakim\Time\Time;

class ApiOsu extends ApiProvider
{

  /**
   * @see https://osu.ppy.sh
   */
  protected const PROVIDER = "osu!";

  /**
   * @see https://osu.ppy.sh/docs/#scopes
   */
  protected array $scopes = [
    "public" => "public",
  ];

  /**
   * @see https://osu.ppy.sh/docs/#ruleset
   */
  public array $rulesets = [
    "osu",
    "taiko",
    "mania",
    "fruits",
  ];

  /**
   * Refreshes the access token of the current instance. If a valid one is
   * still available, it will return the current instance without fetching
   * a new one, unless forced.
   *
   * @param bool $force
   * @return ?static
   * @see https://osu.ppy.sh/docs/#client-credentials-grant
   */
  public function refresh_access_token(bool $force = false)
  {

    # Return the current instance right away, if there is an access token which
    # should not have expired yet. But skip, if forced!
    if ($this->access_token && !Time::over($this->expires_at) && !$force)
      return $this;

    # Build data & headers for the comming cURL request.
    $credentials = oauth_credentials(static::PROVIDER);
    $data = [
      "client_id" => $credentials["client_id"],
      "client_secret" => $credentials["client_secret"],
      "grant_type" => "client_credentials",
      "scope" => $this->scopes["public"],
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

    $this->access_token = $response->access_token;
    $this->expires_at = Time::add($response->expires_in);
    $this->save();

    return $this;
  }

  /**
   * @param int $count
   * @return bool
   */
  public function cache_leaderboard(int $count = 1000)
  {

    try {
      $redis_cache_key = ApiRegistry::$redis_map["osu!"]["ranking"];
      $leaderboard = $this->leaderboard(count: $count);

      foreach ($leaderboard as $ruleset => $users) {
        foreach ($users as $user) {

          # Add the user to a list (sAdd) with key consisting of the cache base
          # key and the ruleset. This will sum up to 4 different lists later.
          $this->redis()->sAdd("$redis_cache_key:$ruleset", (int) $user);
        }
      }

      return true;
    } catch (ApiException $e) {
      new $e;
      return false;
    }
  }

  /**
   * Utilizes a cURL request to fetch the leaderboards from official osu! ser-
   * vers. It will return the $count for ANY ruleset passed, so when, for ex-
   * ample fetching all rulesets (4), it will return an array of 400 user_ids.
   *
   * @param int $count 50, 100, 150, …
   * @param ?string $rulesets osu, taiko, mania, fruits
   * @return array<string, array<int, int>>
   *         [
   *            "osu" => [1, 2, 3, 4], …
   *         ]
   */
  public function leaderboard(int $count = 50, ?array $rulesets = null)
  {

    $final = [];

    # One request will atleast fetch 50 players.
    if ($count < 50)
      $count = 50;

    # Pages to iterate through can be determined by the set count, when
    # divisible by 50. Otherwise we fallback to 1.
    $pages = ($count % 50 === 0 ? $count / 50 : 1);

    # Based on the mode set in params, we iterate through either the one
    # set or when null, all.
    foreach ((!$rulesets ? $this->rulesets : $rulesets) as $ruleset) {
      $final[$ruleset] = [];

      for ($page = 1; $page <= $pages; $page++) {
        $top50 = $this->top_50(mode: $ruleset, page: $page);
        foreach ($top50 ?? [] as $rank)
          $final[$ruleset][] = $rank->user->id;

        # osu! permits one request per second, so throttle the execu-
        # tion by exactly one second 🙂.
        sleep(1);
      }
    }

    return $final;
  }

  /**
   * Fetch leaderboards from osu! API. One request will return 50 players.
   *
   * @param string $mode
   * @param int $page
   * @return ?object
   * @see https://osu.ppy.sh/docs/#get-ranking
   */
  public function top_50(string $mode = "osu", int $page = 1)
  {

    # Build data & headers for the comming cURL request.
    $credentials = oauth_credentials(static::PROVIDER);
    $headers = [
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Bearer ' . ($this->access_token),
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
