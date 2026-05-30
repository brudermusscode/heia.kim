<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;
use Bruder\Geo\Geo;
use Bruder\Utils\Utils;
use Bruder\Application\Cookie;
use Bruder\Application\CurrentUser;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Trait\HasDefaultUser;

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
    "__uid__",
    "__utk__",
  ];

  /**
   * Default values to start a session with
   *
   * @var object
   */
  protected static $default_values = [
    "id" => 0,
    "priv" => 0,
    "clan_id" => 0,
    "clan_priv" => 0,
    "image" => 'default',
    "privacy" => [
      "accepts_policies" => false
    ]
  ];

  /**
   * Just needs the user_id to passed with the $params object.
   *
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {
    $User = null;

    /**
     * @var ?User
     */
    if (!empty($params->user_id))
      $User = User::find($params->user_id ?? 0);
    else if (!empty($params->login) && !empty($params->password)) {

      /**
       * Login params empty?
       * ! Error
       */
      if (!$params->login || !$params->password)
        return $this->error("Fill out all fields!");

      $password = htmlspecialchars_decode($params->password);
      $User = User::verify_login($params->login, $password);
    }

    /**
     * User exists?
     * ! Error
     */
    if (!$User)
      return $this->error("Your credentials are wrong, try again!");

    /**
     * @var array
     */
    $Geo = $params->geodata ?? (new Geo)->get();

    /**
     * @var object
     */
    $device = Utils::detect_browser();

    /**
     * Unique token
     */
    $token = Utils::random_alpha_token(100);

    /**
     * @var Session
     */

    /**
     * Session with similiar info exists?
     */
    $Session = $User->sessions()
      ->where([
        "user_id" => $User->id,
        "os" => $device->os_name ?? null,
        "os_type" => $device->os_type ?? null,
        "os_title" => $device->os_title ?? null,
        "device_type" => $device->device_type ?? null,
        "city" => $Geo["city"] ?? null,
        "postal_code" => $Geo["postCode"] ?? null,
        "country" => $Geo["countryCode"] ?? null,
      ])
      ->first();

    /**
     * Either update the existing session or create a new one.
     */
    if ($Session)
      $Session->update([
        "token" => $token,
        "remote_address" => $Geo["request"] ?? null,
        "browser" => $device->browser_name ?? null,
        "browser_version" => $device->browser_version ?? null,
        "deleted_at" => null,
      ]);
    else
      $Session = $User->sessions()
        ->create([
          "token" => $token,
          "remote_address" => $Geo["request"] ?? null,
          "browser" => $device->browser_name ?? null,
          "browser_version" => $device->browser_version ?? null,
          "os" => $device->os_name ?? null,
          "os_type" => $device->os_type ?? null,
          "os_title" => $device->os_title ?? null,
          "device_type" => $device->device_type ?? null,
          "city" => $Geo["city"] ?? null,
          "postal_code" => $Geo["postCode"] ?? null,
          "country" => $Geo["countryCode"] ?? null,
          "region" => $Geo["region"] ?? null,
          "continent" => null,
          "timezone" => $Geo["timezone"] ?? null,
          "updated_at" => null,
        ]);

    /**
     * Get fresh data.
     */
    $Session = $Session->fresh();

    /**
     * Append to PHP session cookie.
     */
    $_SESSION["session"] = $Session->data();

    /**
     * @var array
     */
    $return_data = [
      "user" => [
        "id" => $User->id,
        "priv" => $User->priv,
        "accepts_policies" => $User->privacy->accepts_policies,
      ]
    ];

    /**
     * Set cookies to persist the session.
     */
    Cookie::set(CurrentUser::$persistent_cookies[0], $User->id, "+10 months", samesite: "Lax");
    Cookie::set(CurrentUser::$persistent_cookies[1], $token, "+10 months", samesite: "Lax");
    Cookie::set(CurrentUser::$persistent_cookies[2], $User->id, "+10 months", samesite: "Lax");
    Cookie::set(CurrentUser::$persistent_cookies[3], $token, "+10 months", samesite: "Lax");

    return $this->success(
      "<strong>Welcome back, " . $User->name . "!</strong> Really great to see you again.",
      data: $return_data,
    );
  }

  /**
   * @param object $params
   * @return object
   */
  public function remove(object $params)
  {

    /**
     * @var bool
     */
    $is_current_session = Cookie::get(CurrentUser::$persistent_cookies[1]) === $params->token;

    /**
     * Check if the token is the same as the one in the cookie.
     */
    if ($is_current_session)
      CurrentUser::logout();

    /**
     * Soft-delete the session instance!
     */
    $this->update([
      "deleted_at" => $this->current_timestamp(),
    ]);

    return $this->success(
      $is_current_session ? "<strong>See you soon, friend!</strong>" : "<strong>You have been logged out on this device!</strong>",
      data: ["current_session" => $is_current_session]
    );
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @return object
   */
  public function display()
  {
    return (object) [
      "icon_class" => match (strtolower($this->os ?? "")) {
        "windows" => "ri-microsoft-fill",
        "linux" => "ri-ubuntu-fill",
        "ios" => "ri-apple-fill",
        "android" => "ri-android-fill",
        "macos" => "ri-finder-fill",
        default => "ri-mac-fill",
      },
      "os_full" => $this->os ?? "Unknown OS " . "-" . ucfirst($this->os_type ?? " N/A"),
      "browser_icon_class" => match (strtolower($this->browser ?? "")) {
        "firefox" => "ri-firefox-fill",
        "chrome" => "ri-chrome-fill",
        "edge" => "ri-edge-new-fill",
        "opera" => "ri-opera-fill",
        "safari",
        "safari mobile" => "ri-safari-fill",
        default => "ri-question-fill",
      },
    ];
  }
}
