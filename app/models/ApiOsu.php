<?php

/**
 * Interact with osu! API in general fashion.
 */

namespace Heiakim\Model;

use Heiakim\Exception\ApiException;
use Heiakim\Registry\ApiRegistry;
use Heiakim\Time\Time;
use Heiakim\Http\CURL;

class ApiOsu extends ApiProvider
{

  /**
   * @see https://osu.ppy.sh
   */
  protected const PROVIDER = "osu!";

  /**
   * osu! API specifications.
   */
  public static array $api = [
    "general" => [
      "endpoint" => "https://osu.ppy.sh/api/v2",
    ],
    "auth" => [
      "endpoint" => "https://osu.ppy.sh/oauth/token",
      "grant_type" => "client_credentials",
    ],
    "user-auth" => [
      "endpoint" => "https://osu.ppy.sh/oauth/authorize",
      "response_type" => "code",
    ],
    "user-access" => [
      "endpoint" => "https://osu.ppy.sh/oauth/token",
      "grant_type" => "authorization_code"
    ],
    "user-refresh-access" => [
      "endpoint" => "https://osu.ppy.sh/oauth/token",
      "grant_type" => "refresh_token",
    ],
  ];

  /**
   * @see https://osu.ppy.sh/docs/#scopes
   */
  public static array $scopes = [
    "identify" => "identify",
    "public" => "public",
  ];

  /**
   * @see https://osu.ppy.sh/docs/#ruleset
   */
  public static array $rulesets = [
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

    $c = oauth_credentials(static::PROVIDER);
    $response = CURL::start(
      url: static::$api["auth"]["endpoint"],
      data: [
        "client_id" => $c["client_id"],
        "client_secret" => $c["client_secret"],
        "grant_type" => "client_credentials",
        "scope" => static::$scopes["public"],
      ],
      options: [
        CURLOPT_HTTPHEADER => [
          'Accept: application/json',
          'Content-Type: application/x-www-form-urlencoded',
        ],
      ]
    );

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
    foreach ((!$rulesets ? static::$rulesets : $rulesets) as $ruleset) {
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

    # Refresh the access token first 🙂 Will only refresh if the old is expired.
    $this->refresh_access_token();

    # Start the cURL request.
    $response = CURL::start(
      url: static::$api["general"]["endpoint"]
        . "/rankings/$mode/performance?cursor[page]=$page",
      type: "GET",
      options: [
        CURLOPT_HTTPHEADER => [
          'Accept: application/json',
          'Content-Type: application/json',
          'Authorization: Bearer ' . ($this->access_token),
        ]
      ],
    );

    # No user inside a ranking object set?
    if (empty($response->ranking[0]->user))
      return null;

    return $response->ranking;
  }
}
