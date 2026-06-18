<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Geo\Geo;
use Heiakim\Utils\Utils;
use Heiakim\Application\Cookie;
use Heiakim\Application\CurrentUser;
use Heiakim\Application\Session as ApplicationSession;
use Heiakim\Http\Request;
use Heiakim\Model\User;
use Heiakim\Trait\HasDefaultUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "token",
    "remote_address",
    "browser",
    "browser_version",
    "browser_title",
    "os",
    "os_type",
    "os_title",
    "device_type",
    "city",
    "postal_code",
    "country",
    "region",
    "continent",
    "timezone",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $persistent_cookies = [
    "__user_id",
    "__user_token",
  ];

  /**
   * @param User $User
   * @return User
   */
  public function new(User $User)
  {

    /**
     * @var self
     */
    $Session = self::make();

    $ip = Request::get_remote_address();
    $country_code = Geo::country_code($ip);
    $token = Utils::random_alpha_token(42);

    # Set all values.
    $Session->user_id = $User->id;
    $Session->token = $token;
    $Session->remote_address = $ip;
    $Session->country = $country_code;

    # Save & persist!
    $Session->save();
    $Session->persist();

    $success = "<strong>Great to see you again " . $User->name . "!</strong>";

    return $User;
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params)
  {
    return success();
  }

  /**
   * @param ?int $user_id
   * @param ?string $token
   * @return ?self
   */
  public static function valid(?int $user_id = null, ?string $token = null)
  {

    # Anything is missing?
    if (!$user_id || !$token)
      return null;

    $Session = self::where([
      "user_id" => $user_id,
      "token" => $token
    ])
      ->whereNull("deleted_at")
      ->first();

    $PHPSession = \Heiakim\Application\Session::get("Session");

    if (!$Session || !$PHPSession || !$PHPSession->is($Session))
      return null;

    return $Session;
  }

  /**
   * @return bool
   */
  public function persist()
  {

    # Set User & Session to the PHP session object.
    $_SESSION["Session"] = $this;
    $_SESSION["User"] = $this->user->fresh();

    # Reset the persistent cookies.
    Cookie::set(self::$persistent_cookies[1], $this->token, "+1 year", samesite: "Lax");
    Cookie::set(self::$persistent_cookies[0], $this->user->id, "+1 year", samesite: "Lax");
  }

  /**
   * Cleans up all the cookies and left over sessions that do not
   * belong to a possible instance.
   *
   * @return void
   */
  public static function clean_up()
  {

    foreach (self::$persistent_cookies as $cookie)
      Cookie::delete($cookie);

    ApplicationSession::remove("User");
    ApplicationSession::remove("Session");
  }

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return string
   */
  public function masked_ip()
  {
    $masked_ip = $this->remote_address;
    $masked_ip_split = explode(".", $masked_ip);
    unset($masked_ip_split[array_key_last($masked_ip_split)]);
    $masked_ip = implode(".", $masked_ip_split);

    return $masked_ip;
  }
}
