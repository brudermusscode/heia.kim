<?php

namespace Heiakim\Trait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Heiakim\Exception\APIException;
use Heiakim\Model\ConnectionDiscord;
use Heiakim\Model\ConnectionGoogle;
use Heiakim\Model\ConnectionOsu;
use Heiakim\Model\User;

trait IsProviderConnection
{

  /**
   * @var string
   */
  protected $table = "connections";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "provider",
    "is_legit",
    "provider_user_id",
    "provider_user_email",
    "provider_user_nickname",
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
   * Traits do not inherit the Eloquent Model base class, so there will be squ-
   * iggles when using belongTo() or any other function on $this. This function
   * lives to prevent this.
   *
   * @return \Illuminate\Database\Eloquent\Model
   */
  public function model()
  {
    return $this;
  }

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Reverse searches the provider mapping and gets the string for the provider
   * and based on that, fetches the oauth credentials from the config file lo-
   * cated in /config/security.
   *
   * @return array
   * @throws \Exception
   */
  public static function oauth_credentials()
  {
    $provider_key_string = self::provider_map_key();
    $arr = require _root() . "/config/security/oauth.php";

    return $arr[$provider_key_string] ?? [];
  }

  /**
   * @param string $provider
   * @return ?string
   */
  public static function map_provider(string $provider)
  {
    return self::$provider_class_map[strtolower($provider)] ?? null;
  }

  /**
   * Receives the key string from the class mapping for providers of available
   * oauth.
   *
   * @return ?string
   * @throws APIException
   */
  public static function provider_map_key()
  {
    return array_search(static::class, self::$provider_class_map, strict: true)
      ?? throw new APIException("Invalid Provider for fetching oauth credentials.");
  }

  /**
   * Validates the given provider through the POST params and does return the
   * corresponding class-string.
   *
   * @return class-string<ConnectionDiscord|ConnectionOsu|ConnectionGoogle>
   *
   * NOTE: Will die on error.
   */
  public static function ProviderClassOrDie(string $provider)
  {
    return self::map_provider($provider) ?? die(error("!INVALID_API_CALL"));
  }
}
