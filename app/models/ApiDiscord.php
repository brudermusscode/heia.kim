<?php

/**
 * Interact with Discord API in general fashion.
 */

namespace Heiakim\Model;

use Heiakim\Time\Time;
use Heiakim\Http\CURL;

class ApiDiscord extends ApiProvider
{

  /**
   * @see https://discord.com
   */
  protected const PROVIDER = "discord";

  /**
   * @see https://docs.discord.com/developers/topics/oauth2
   */
  public static array $api = [
    "general" => [
      "endpoint" => "https://discord.com/api/v10",
    ],
    "auth" => [
      "endpoint" => "https://discord.com/api/oauth2/token",
      "grant_type" => "client_credentials",
    ],
    "user-auth" => [
      "endpoint" => "https://discord.com/oauth2/authorize",
      "response_type" => "code",
    ],
    "user-access" => [
      "endpoint" => "https://discord.com/api/oauth2/token",
      "grant_type" => "authorization_code"
    ],
    "user-refresh-access" => [
      "endpoint" => "https://discord.com/api/oauth2/token",
      "grant_type" => "refresh_token",
    ],
  ];

  /**
   * @see https://docs.discord.com/developers/platform/oauth2-and-permissions#scopes
   */
  public static array $scopes = [
    "identify" => "identify",
    "email" => "email",
    "guilds" => "guilds",
    "guilds.join" => "guilds.join",
  ];
}
