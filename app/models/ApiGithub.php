<?php

/**
 * Interact with GitHub API in general fashion.
 */

namespace Heiakim\Model;

use Heiakim\Time\Time;
use Heiakim\Http\CURL;

class ApiGithub extends ApiProvider
{

  /**
   * @see https://github.com
   */
  protected const PROVIDER = "github";

  /**
   * @see https://docs.github.com/en/apps/oauth-apps/building-oauth-apps
   */
  public static array $api = [
    "general" => [
      "endpoint" => "https://api.github.com",
    ],
    "user-auth" => [
      "endpoint" => "https://github.com/login/oauth/authorize",
      "response_type" => "code",
    ],
    "user-access" => [
      "endpoint" => "https://github.com/login/oauth/access_token",
    ],
  ];

  /**
   * @see https://docs.github.com/en/apps/oauth-apps/building-oauth-apps/scopes-for-oauth-apps
   */
  public static array $scopes = [
    "read:user" => "read:user",
    "user:email" => "user:email",
  ];
}
