<?php

/**
 * This is the base class for any vendor API connection through
 * OAuth2 standarts. It represents the actual model connected
 * with a database table `connections`. Any vendor will inherit
 * this class and gain all functionality from it.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Application\Logger;
use Heiakim\Exception\APIException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Heiakim\Model\User;

class Connection extends Justin
{

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "provider",

    # is_legit will be set to 1, if the user connects an osu! account,
    # which is in the top 1000 of the official osu! leaderboards.
    "is_legit",
    # ---------

    "provider_user_id",
    "provider_user_email",
    "access_token",
    "refresh_token",
    "token_id",
    "scope",
    "expires_at",
    "deleted_at",
    "updated_at",
  ];

  /**
   * Maps the provider name from POST params to an existing class.
   */
  protected static array $provider_class_map = [
    "discord" => \Heiakim\Model\ConnectionDiscord::class,
    "google" => \Heiakim\Model\ConnectionGoogle::class,
    "osu!" => \Heiakim\Model\ConnectionOsu::class,
  ];

  /**
   * @param string $provider
   * @return ?string
   */
  public static function map_provider(string $provider)
  {
    return self::$provider_class_map[strtolower($provider)] ?? null;
  }

  /**
   * Reverse searches the provider mapping and gets the string for the
   * provider and based on that, fetches the oauth credentials from the
   * config file located in /config/security.
   *
   * @return array
   * @throws \Exception
   */
  public static function oauth_credentials()
  {

    $provider = array_search(static::class, self::$provider_class_map, strict: true);

    if (!$provider)
      throw new APIException("Invalid Provider for fetching oauth credentials.");

    $arr = require _root() . "/config/security/oauth.php";

    return $arr[$provider] ?? [];
  }

  # -------------------------------------------------------------------

  # Removed: get_auth_uri and Vendor class validation. This has been moved
  # to a general ConnectionsController. Validation should not happen in a
  # model itself. The model should just be responsible for managing an in-
  # stance's state by creating new, updating or deleting existing. Maybe
  # some more.

  # Removed: new method, as this will happen in each of the vendor class
  # that inherits this base class. As any vendor tries to follow OAuth2-
  # standarts, there iis still some uniqueness to any API.

  # -------------------------------------------------------------------

  # Following functions need to be available in the sub classes:
  # generate_link(): string

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
