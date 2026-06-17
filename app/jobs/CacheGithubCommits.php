<?php

namespace Heiakim\Job;

use Heiakim\Job;
use Heiakim\Database\RedisManager;
use Heiakim\Registry\RedisRegistry;

class CacheGithubCommits extends Job
{

  /**
   * @return void
   */
  public static function run(string $interval = "+1 day")
  {

    if (static::has_run_before($interval)) return;

    $amount = 12;
    $owner = "brudermusscode";
    $repo = "heia.kim";
    $url = "https://api.github.com/repos/$owner/$repo/commits?per_page=$amount";

    $ch = curl_init($url);

    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "User-Agent: " . _env("APP_NAME"),
        "Accept: application/vnd.github+json",
        "Authorization: Bearer " . _env("GITHUB_PAT"),
      ]
    ]);

    # NOTE: Might return false.
    $response = curl_exec($ch);
    $commits = json_decode($response, true);

    if (!$response || empty($commits[0]["commit"])) {
      echo "failed.\n";
      return;
    }

    /**
     * @var \Redis
     */
    $Redis = new RedisManager()->connection();
    $Redis->del(RedisRegistry::$github_commit_history);

    foreach ($commits as $commit) {
      $timestamp = strtotime($commit["commit"]["author"]["date"] ?? date("now"));
      $Redis->zAdd(
        RedisRegistry::$github_commit_history,
        $timestamp,
        json_encode($commit)
      );
    }

    # Success!
    echo "done!\n";
  }
}
